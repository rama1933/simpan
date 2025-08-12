<?php

namespace App\Services\Knowledge;

use App\Models\KnowledgeVersion;
use App\Models\KnowledgeVersionAttachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KnowledgeVersionService
{
    /**
     * Create a new knowledge version
     */
    public function createVersion($data)
    {
        return DB::transaction(function () use ($data) {
            // Get latest version number
            $latestVersion = KnowledgeVersion::where('knowledge_id', $data['knowledge_id'])
                ->max('version_number') ?? 0;

            // Create new version
            $version = KnowledgeVersion::create([
                'knowledge_id' => $data['knowledge_id'],
                'version_number' => $latestVersion + 1,
                'title' => $data['title'],
                'content' => $data['content'],
                'summary' => $data['summary'] ?? null,
                'category_id' => $data['category_id'],
                'skpd_id' => $data['skpd_id'],
                'status' => $data['status'] ?? 'draft',
                'verification_status' => 'pending',
                'change_type' => $data['change_type'],
                'change_reason' => $data['change_reason'],
                'effective_date' => $data['effective_date'] ?? null,
                'expiry_date' => $data['expiry_date'] ?? null,
                'created_by' => Auth::id(),
                'is_current' => false,
            ]);

            // Attach tags if provided
            if (isset($data['tags']) && is_array($data['tags'])) {
                $version->tags()->attach($data['tags']);
            }

            return $version;
        });
    }

    /**
     * Update knowledge version
     */
    public function updateVersion($version, $data)
    {
        return DB::transaction(function () use ($version, $data) {
            $version->update([
                'title' => $data['title'],
                'content' => $data['content'],
                'summary' => $data['summary'] ?? null,
                'category_id' => $data['category_id'],
                'skpd_id' => $data['skpd_id'],
                'change_type' => $data['change_type'],
                'change_reason' => $data['change_reason'],
                'effective_date' => $data['effective_date'] ?? null,
                'expiry_date' => $data['expiry_date'] ?? null,
            ]);

            // Update tags
            if (isset($data['tags']) && is_array($data['tags'])) {
                $version->tags()->sync($data['tags']);
            }

            return $version;
        });
    }

    /**
     * Publish a version
     */
    public function publishVersion($version)
    {
        return DB::transaction(function () use ($version) {
            // Set current version as not current
            KnowledgeVersion::where('knowledge_id', $version->knowledge_id)
                ->where('is_current', true)
                ->update(['is_current' => false]);

            // Publish this version
            $version->update([
                'status' => 'published',
                'is_current' => true,
            ]);

            return $version;
        });
    }

    /**
     * Archive a version
     */
    public function archiveVersion($version)
    {
        $version->update(['status' => 'archived']);
        return $version;
    }

    /**
     * Verify a version
     */
    public function verifyVersion($version, $notes = null)
    {
        $version->update([
            'verification_status' => 'verified',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'verification_notes' => $notes,
        ]);

        return $version;
    }

    /**
     * Reject a version
     */
    public function rejectVersion($version, $reason)
    {
        $version->update([
            'verification_status' => 'rejected',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return $version;
    }

    /**
     * Handle file attachments
     */
    public function handleAttachments($version, $files)
    {
        if (!$files || !is_array($files)) {
            return;
        }

        foreach ($files as $file) {
            if (!$file->isValid()) {
                continue;
            }

            // Generate unique filename
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Store file in private directory
            $path = $file->storeAs('knowledge-versions/attachments', $filename, 'private');

            // Create attachment record
            KnowledgeVersionAttachment::create([
                'knowledge_version_id' => $version->id,
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $filename,
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_by' => Auth::id(),
            ]);
        }
    }

    /**
     * Remove attachments
     */
    public function removeAttachments($attachmentIds)
    {
        if (!$attachmentIds || !is_array($attachmentIds)) {
            return;
        }

        $attachments = KnowledgeVersionAttachment::whereIn('id', $attachmentIds)->get();
        
        foreach ($attachments as $attachment) {
            // Delete file from storage
            if (Storage::disk('private')->exists($attachment->file_path)) {
                Storage::disk('private')->delete($attachment->file_path);
            }
            
            // Delete record
            $attachment->delete();
        }
    }

    /**
     * Get version statistics
     */
    public function getVersionStatistics($knowledgeId = null)
    {
        $query = KnowledgeVersion::query();
        
        if ($knowledgeId) {
            $query->where('knowledge_id', $knowledgeId);
        }

        return [
            'total' => $query->count(),
            'draft' => $query->where('status', 'draft')->count(),
            'published' => $query->where('status', 'published')->count(),
            'archived' => $query->where('status', 'archived')->count(),
            'withdrawn' => $query->where('status', 'withdrawn')->count(),
            'pending_verification' => $query->where('verification_status', 'pending')->count(),
            'verified' => $query->where('verification_status', 'verified')->count(),
            'rejected' => $query->where('verification_status', 'rejected')->count(),
        ];
    }

    /**
     * Get version history for a knowledge
     */
    public function getVersionHistory($knowledgeId)
    {
        return KnowledgeVersion::where('knowledge_id', $knowledgeId)
            ->with(['creator', 'verifier', 'attachments'])
            ->orderBy('version_number', 'desc')
            ->get();
    }

    /**
     * Compare two versions
     */
    public function compareVersions($version1Id, $version2Id)
    {
        $version1 = KnowledgeVersion::findOrFail($version1Id);
        $version2 = KnowledgeVersion::findOrFail($version2Id);

        return [
            'version1' => $version1,
            'version2' => $version2,
            'differences' => [
                'title' => $version1->title !== $version2->title,
                'content' => $version1->content !== $version2->content,
                'summary' => $version1->summary !== $version2->summary,
                'category' => $version1->category_id !== $version2->category_id,
                'skpd' => $version1->skpd_id !== $version2->skpd_id,
            ]
        ];
    }
}