<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
        ];

        // Aturan untuk 'code' berbeda antara store dan update
        if ($this->isMethod('post')) {
            $rules['code'] = ['required', 'string', Rule::in(['SA', 'SI', 'GM', 'KL', 'KR'])];
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['code'] = ['sometimes', 'string', Rule::in(['SA', 'SI', 'GM', 'KL', 'KR'])];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'code.required'      => 'Kode kategori wajib diisi.',
            'code.in'            => 'Kode kategori tidak valid. Pilih dari: SA, SI, GM, KL, KR.',
            'name.required'      => 'Nama kategori wajib diisi.',
            'name.string'        => 'Nama kategori harus berupa teks.',
            'name.max'           => 'Nama kategori maksimal 255 karakter.',
            'description.required' => 'Deskripsi kategori wajib diisi.',
            'description.string' => 'Deskripsi kategori harus berupa teks.',
        ];
    }
}
