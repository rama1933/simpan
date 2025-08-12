<?php

namespace App\Http\Requests\Knowledge;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class StoreKnowledgeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $user = Auth::user();
        
        // If user is SKPD (not Admin), force their skpd_id
        if ($user instanceof User) {
            if ($user->hasRole('User SKPD') && $user->skpd_id) {
                $this->merge([
                    'skpd_id' => $user->skpd_id
                ]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'content' => 'required|string|min:10',
            'category_id' => 'required|integer|exists:categories,id',
            'skpd_id' => 'required|integer|exists:master_skpds,id',
            'status' => 'required|in:draft,published,archived',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'attachments' => 'nullable|array',
            // Per-file maksimal 5MB, jenis: dokumen & gambar
            'attachments.*' => 'file|max:5120|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $files = (array) $this->file('attachments', []);
            $totalBytes = 0;
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $totalBytes += (int) $file->getSize();
                }
            }
            $maxTotal = 5 * 1024 * 1024; // 5 MB
            if ($totalBytes > $maxTotal) {
                $v->errors()->add('attachments', 'Total ukuran lampiran maksimal 5MB.');
            }
        });
    }
}

