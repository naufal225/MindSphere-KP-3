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
        $validCodes = $this->validCodes();

        $rules = [
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
        ];

        // // Aturan untuk 'code' berbeda antara store dan update
        // if ($this->isMethod('post')) {
        //     $rules['code'] = ['required', 'string'];
        //     if (!empty($validCodes)) {
        //         $rules['code'][] = Rule::in($validCodes);
        //     }
        // } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
        //     $rules['code'] = ['sometimes', 'string'];
        //     if (!empty($validCodes)) {
        //         $rules['code'][] = Rule::in($validCodes);
        //     }
        // }

        return $rules;
    }

    public function messages()
    {
        $codesList = implode(', ', $this->validCodes());

        return [
            'code.required'      => 'Kode kategori wajib diisi.',
            'code.in'            => $codesList
                ? 'Kode kategori tidak valid. Pilih dari: ' . $codesList . '.'
                : 'Kode kategori tidak valid.',
            'name.required'      => 'Nama kategori wajib diisi.',
            'name.string'        => 'Nama kategori harus berupa teks.',
            'name.max'           => 'Nama kategori maksimal 255 karakter.',
            'description.required' => 'Deskripsi kategori wajib diisi.',
            'description.string' => 'Deskripsi kategori harus berupa teks.',
        ];
    }

    protected function validCodes(): array
    {
        return array_keys(config('category.codes', []));
    }
}
