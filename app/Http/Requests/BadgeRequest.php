<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BadgeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name'          => 'required|string|max:255',
            'description'   => 'required|string',
            'category_id'   => 'required|exists:categories,id',
            'xp_required'   => 'nullable|integer|min:1',
        ];

        // Aturan untuk icon: required saat create, optional saat update
        if ($this->isMethod('post')) {
            $rules['icon'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'; // max 2MB
        } else {
            $rules['icon'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required'         => 'Nama badge wajib diisi.',
            'name.string'           => 'Nama badge harus berupa teks.',
            'name.max'              => 'Nama badge maksimal 255 karakter.',

            'description.required'  => 'Deskripsi badge wajib diisi.',
            'description.string'    => 'Deskripsi badge harus berupa teks.',

            'category_id.required'  => 'Kategori badge wajib dipilih.',
            'category_id.exists'    => 'Kategori yang dipilih tidak ditemukan.',

            'xp_required.integer'   => 'XP yang dibutuhkan harus berupa angka.',
            'xp_required.min'       => 'XP yang dibutuhkan minimal 1.',

            'icon.required'         => 'Ikon badge wajib diunggah.',
            'icon.image'            => 'File harus berupa gambar.',
            'icon.mimes'            => 'Format gambar harus JPEG, PNG, JPG, GIF, atau SVG.',
            'icon.max'              => 'Ukuran gambar maksimal 2 MB.',
        ];
    }
}
