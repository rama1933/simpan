<?php

namespace App\Observers;

use App\Models\Knowledge;
use App\Services\ChangeLog\ChangeLogService;
use Illuminate\Support\Facades\Auth;

class KnowledgeChangeObserver
{
    public function __construct(
        private ChangeLogService $changeLogService
    ) {}

    /**
     * Handle the Knowledge "created" event.
     */
    public function created(Knowledge $knowledge): void
    {
        $this->changeLogService->logChange(
            $knowledge,
            'created',
            null,
            null,
            null,
            'Pengetahuan baru dibuat: ' . $knowledge->title,
            [
                'title' => $knowledge->title,
                'category_id' => $knowledge->category_id,
                'status' => $knowledge->status,
                'author_id' => $knowledge->author_id
            ]
        );
    }

    /**
     * Handle the Knowledge "updated" event.
     */
    public function updated(Knowledge $knowledge): void
    {
        $changes = $this->changeLogService->compareModels(
            $knowledge->getOriginal() ? new Knowledge($knowledge->getOriginal()) : $knowledge,
            $knowledge
        );

        if (!empty($changes)) {
            // Log general update
            $this->changeLogService->logChange(
                $knowledge,
                'updated',
                null,
                null,
                null,
                'Pengetahuan diperbarui: ' . $knowledge->title,
                [
                    'changed_fields' => array_keys($changes),
                    'total_changes' => count($changes)
                ]
            );

            // Log specific field changes
            foreach ($changes as $field => $values) {
                $this->logSpecificFieldChange($knowledge, $field, $values['old'], $values['new']);
            }
        }
    }

    /**
     * Handle the Knowledge "deleted" event.
     */
    public function deleted(Knowledge $knowledge): void
    {
        $this->changeLogService->logChange(
            $knowledge,
            'deleted',
            null,
            null,
            null,
            'Pengetahuan dihapus: ' . $knowledge->title,
            [
                'title' => $knowledge->title,
                'category_id' => $knowledge->category_id,
                'status' => $knowledge->status,
                'deleted_by' => Auth::id()
            ]
        );
    }

    /**
     * Handle the Knowledge "restored" event.
     */
    public function restored(Knowledge $knowledge): void
    {
        $this->changeLogService->logChange(
            $knowledge,
            'restored',
            null,
            null,
            null,
            'Pengetahuan dipulihkan: ' . $knowledge->title,
            [
                'title' => $knowledge->title,
                'restored_by' => Auth::id()
            ]
        );
    }

    /**
     * Log specific field changes with custom descriptions
     */
    private function logSpecificFieldChange(Knowledge $knowledge, string $field, $oldValue, $newValue): void
    {
        $description = $this->getFieldChangeDescription($field, $oldValue, $newValue, $knowledge->title);
        
        $this->changeLogService->logChange(
            $knowledge,
            'field_updated',
            $field,
            $oldValue,
            $newValue,
            $description,
            [
                'field_type' => $this->getFieldType($field),
                'knowledge_title' => $knowledge->title
            ]
        );
    }

    /**
     * Get human-readable description for field changes
     */
    private function getFieldChangeDescription(string $field, $oldValue, $newValue, string $knowledgeTitle): string
    {
        $userName = Auth::user()?->name ?? 'System';
        
        switch ($field) {
            case 'title':
                return "{$userName} mengubah judul dari '{$oldValue}' menjadi '{$newValue}'";
            
            case 'content':
                return "{$userName} memperbarui konten pengetahuan '{$knowledgeTitle}'";
            
            case 'description':
                return "{$userName} mengubah deskripsi pengetahuan '{$knowledgeTitle}'";
            
            case 'status':
                return "{$userName} mengubah status dari '{$oldValue}' menjadi '{$newValue}' untuk '{$knowledgeTitle}'";
            
            case 'verification_status':
                return "{$userName} mengubah status verifikasi dari '{$oldValue}' menjadi '{$newValue}' untuk '{$knowledgeTitle}'";
            
            case 'category_id':
                return "{$userName} mengubah kategori pengetahuan '{$knowledgeTitle}'";
            
            case 'tags':
                return "{$userName} memperbarui tag pengetahuan '{$knowledgeTitle}'";
            
            case 'published_at':
                if ($newValue && !$oldValue) {
                    return "{$userName} mempublikasikan pengetahuan '{$knowledgeTitle}'";
                } elseif (!$newValue && $oldValue) {
                    return "{$userName} membatalkan publikasi pengetahuan '{$knowledgeTitle}'";
                }
                return "{$userName} mengubah tanggal publikasi pengetahuan '{$knowledgeTitle}'";
            
            case 'verified_at':
                if ($newValue && !$oldValue) {
                    return "{$userName} memverifikasi pengetahuan '{$knowledgeTitle}'";
                }
                return "{$userName} mengubah status verifikasi pengetahuan '{$knowledgeTitle}'";
            
            case 'verification_note':
                return "{$userName} menambahkan/mengubah catatan verifikasi untuk '{$knowledgeTitle}'";
            
            default:
                return "{$userName} mengubah {$field} untuk pengetahuan '{$knowledgeTitle}'";
        }
    }

    /**
     * Get field type for metadata
     */
    private function getFieldType(string $field): string
    {
        $fieldTypes = [
            'title' => 'string',
            'content' => 'text',
            'description' => 'text',
            'status' => 'enum',
            'verification_status' => 'enum',
            'category_id' => 'foreign_key',
            'skpd_id' => 'foreign_key',
            'author_id' => 'foreign_key',
            'verified_by' => 'foreign_key',
            'tags' => 'json',
            'published_at' => 'datetime',
            'verified_at' => 'datetime',
            'verification_note' => 'text'
        ];

        return $fieldTypes[$field] ?? 'unknown';
    }
}