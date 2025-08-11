<?php

namespace App\Http\Requests\Knowledge;

use App\Data\Knowledge\KnowledgeDTO;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateKnowledgeRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = KnowledgeDTO::rules();

        // Make some fields optional for updates
        $rules['title'] = 'sometimes|required|string|max:255';
        $rules['content'] = 'sometimes|required|string|min:10';

        // Attachment rules for update
        $rules['attachments'] = 'nullable|array';
        $rules['attachments.*'] = 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpeg,jpg,png|max:5120';
        $rules['remove_attachment_ids'] = 'nullable|array';
        $rules['remove_attachment_ids.*'] = 'integer|exists:knowledge_attachments,id';

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul pengetahuan wajib diisi.',
            'title.max' => 'Judul pengetahuan maksimal 255 karakter.',
            'content.required' => 'Konten pengetahuan wajib diisi.',
            'content.min' => 'Konten pengetahuan minimal 10 karakter.',
            'description.max' => 'Deskripsi pengetahuan maksimal 500 karakter.',
            'category_id.required' => 'Kategori pengetahuan wajib diisi.',
            'category_id.exists' => 'Kategori tidak valid.',
            'skpd_id.required' => 'SKPD pengetahuan wajib diisi.',
            'skpd_id.exists' => 'SKPD tidak valid.',
            'tags.array' => 'Tags harus berupa array.',
            'tags.*.string' => 'Setiap tag harus berupa string.',
            'tags.*.max' => 'Setiap tag maksimal 50 karakter.',
            'status.required' => 'Status pengetahuan wajib diisi.',
            'status.in' => 'Status pengetahuan harus draft, published, atau archived.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'judul pengetahuan',
            'content' => 'konten pengetahuan',
            'description' => 'deskripsi pengetahuan',
            'category_id' => 'kategori pengetahuan',
            'skpd_id' => 'SKPD',
            'tags' => 'tags pengetahuan',
            'status' => 'status pengetahuan'
        ];
    }
}

