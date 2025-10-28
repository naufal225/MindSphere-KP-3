<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChallengeRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'xp_reward' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ];

        // // Jika ini adalah update (method PUT/PATCH), tambahkan validasi untuk ID
        // if ($this->isMethod('put') || $this->isMethod('patch')) {
        //     $rules['id'] = 'required|exists:challenges,id';
        // }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul tantangan wajib diisi.',
            'title.string' => 'Judul tantangan harus berupa teks.',
            'title.max' => 'Judul tantangan maksimal 255 karakter.',

            'description.required' => 'Deskripsi tantangan wajib diisi.',
            'description.string' => 'Deskripsi tantangan harus berupa teks.',

            'category_id.required' => 'Kategori tantangan wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak ditemukan.',

            'xp_reward.required' => 'Hadiah XP wajib diisi.',
            'xp_reward.integer' => 'Hadiah XP harus berupa angka.',
            'xp_reward.min' => 'Hadiah XP minimal 1.',

            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Format tanggal mulai tidak valid.',

            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.date' => 'Format tanggal selesai tidak valid.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama dengan atau setelah tanggal mulai.',

            'id.required' => 'ID tantangan wajib disediakan saat memperbarui.',
            'id.exists' => 'Tantangan dengan ID tersebut tidak ditemukan.',
        ];
    }
}
