<?php

namespace App\Http\Requests\KnowledgeVersion;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class StoreKnowledgeVersionRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->hasRole(['Admin', 'User SKPD']);
    }

    /**
     * Prepare the data for validation.
     */
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

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'knowledge_id' => 'required|exists:knowledges,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'summary' => 'nullable|string|max:1000',
            'category_id' => 'required|exists:categories,id',
            'skpd_id' => 'required|exists:skpds,id',
            'change_type' => 'required|in:create,update,status_change,delete',
            'change_reason' => 'required|string|max:500',
            'effective_date' => 'nullable|date|after_or_equal:today',
            'expiry_date' => 'nullable|date|after:effective_date',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'attachments' => 'array|max:10',
            'attachments.*' => 'file|max:5120|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'knowledge_id.required' => 'Pengetahuan harus dipilih.',
            'knowledge_id.exists' => 'Pengetahuan yang dipilih tidak valid.',
            'title.required' => 'Judul versi harus diisi.',
            'title.max' => 'Judul versi maksimal 255 karakter.',
            'content.required' => 'Konten versi harus diisi.',
            'summary.max' => 'Ringkasan maksimal 1000 karakter.',
            'category_id.required' => 'Kategori harus dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'skpd_id.required' => 'SKPD harus dipilih.',
            'skpd_id.exists' => 'SKPD yang dipilih tidak valid.',
            'change_type.required' => 'Tipe perubahan harus dipilih.',
            'change_type.in' => 'Tipe perubahan tidak valid.',
            'change_reason.required' => 'Alasan perubahan harus diisi.',
            'change_reason.max' => 'Alasan perubahan maksimal 500 karakter.',
            'effective_date.date' => 'Tanggal efektif harus berupa tanggal yang valid.',
            'effective_date.after_or_equal' => 'Tanggal efektif tidak boleh kurang dari hari ini.',
            'expiry_date.date' => 'Tanggal kadaluarsa harus berupa tanggal yang valid.',
            'expiry_date.after' => 'Tanggal kadaluarsa harus setelah tanggal efektif.',
            'tags.array' => 'Tag harus berupa array.',
            'tags.*.exists' => 'Tag yang dipilih tidak valid.',
            'attachments.array' => 'Lampiran harus berupa array.',
            'attachments.max' => 'Maksimal 10 lampiran yang dapat diunggah.',
            'attachments.*.file' => 'Lampiran harus berupa file.',
            'attachments.*.max' => 'Ukuran lampiran maksimal 5MB.',
            'attachments.*.mimes' => 'Format lampiran harus: pdf, doc, docx, xls, xlsx, jpg, jpeg, png.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'knowledge_id' => 'pengetahuan',
            'title' => 'judul',
            'content' => 'konten',
            'summary' => 'ringkasan',
            'category_id' => 'kategori',
            'skpd_id' => 'SKPD',
            'change_type' => 'tipe perubahan',
            'change_reason' => 'alasan perubahan',
            'effective_date' => 'tanggal efektif',
            'expiry_date' => 'tanggal kadaluarsa',
            'tags' => 'tag',
            'attachments' => 'lampiran',
        ];
    }
}