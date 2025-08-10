<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

abstract class BaseFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    abstract public function rules(): array;

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'required' => 'Field :attribute wajib diisi.',
            'email' => 'Field :attribute harus berupa email yang valid.',
            'min' => 'Field :attribute minimal :min karakter.',
            'max' => 'Field :attribute maksimal :max karakter.',
            'unique' => 'Field :attribute sudah digunakan.',
            'confirmed' => 'Konfirmasi :attribute tidak cocok.',
            'numeric' => 'Field :attribute harus berupa angka.',
            'integer' => 'Field :attribute harus berupa bilangan bulat.',
            'string' => 'Field :attribute harus berupa teks.',
            'array' => 'Field :attribute harus berupa array.',
            'file' => 'Field :attribute harus berupa file.',
            'image' => 'Field :attribute harus berupa gambar.',
            'mimes' => 'Field :attribute harus berupa file dengan tipe: :values.',
            'size' => 'Field :attribute harus berukuran :size KB.',
            'between' => 'Field :attribute harus antara :min dan :max.',
            'exists' => 'Field :attribute tidak valid.',
            'date' => 'Field :attribute harus berupa tanggal yang valid.',
            'date_format' => 'Field :attribute harus sesuai format: :format.',
            'before' => 'Field :attribute harus sebelum :date.',
            'after' => 'Field :attribute harus setelah :date.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama',
            'email' => 'email',
            'password' => 'password',
            'password_confirmation' => 'konfirmasi password',
            'title' => 'judul',
            'content' => 'konten',
            'description' => 'deskripsi',
            'file' => 'file',
            'image' => 'gambar',
            'phone' => 'nomor telepon',
            'address' => 'alamat',
            'city' => 'kota',
            'province' => 'provinsi',
            'postal_code' => 'kode pos',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            );
        }

        parent::failedValidation($validator);
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization(): void
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk melakukan aksi ini.',
                ], JsonResponse::HTTP_FORBIDDEN)
            );
        }

        abort(403, 'Anda tidak memiliki akses untuk melakukan aksi ini.');
    }
}

