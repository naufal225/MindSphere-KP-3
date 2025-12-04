<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRewardRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Pastikan hanya admin yang bisa akses (dilakukan di middleware)
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'coin_cost' => 'required|integer|min:1',
            'stock' => 'required|integer|min:-1',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_url' => 'nullable|url',
            'type' => ['required', Rule::in(['physical', 'digital', 'voucher'])],
            'validity_days' => 'nullable|integer|min:1',
            'additional_info' => 'nullable|array',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama reward wajib diisi',
            'name.max' => 'Nama reward maksimal 255 karakter',
            'description.max' => 'Deskripsi maksimal 1000 karakter',
            'coin_cost.required' => 'Biaya koin wajib diisi',
            'coin_cost.integer' => 'Biaya koin harus berupa angka',
            'coin_cost.min' => 'Biaya koin minimal 1',
            'stock.required' => 'Stok wajib diisi',
            'stock.integer' => 'Stok harus berupa angka',
            'stock.min' => 'Stok minimal -1 (unlimited)',
            'is_active.boolean' => 'Status aktif harus benar atau salah',
            'image.image' => 'File harus berupa gambar',
            'image.mimes' => 'Format gambar harus: jpeg, png, jpg, gif',
            'image.max' => 'Ukuran gambar maksimal 2MB',
            'image_url.url' => 'URL gambar tidak valid',
            'type.required' => 'Tipe reward wajib dipilih',
            'type.in' => 'Tipe reward tidak valid',
            'validity_days.integer' => 'Masa berlaku harus berupa angka',
            'validity_days.min' => 'Masa berlaku minimal 1 hari',
            'additional_info.array' => 'Info tambahan harus berupa array',
        ];
    }

    public function prepareForValidation()
    {
        // Konversi string boolean ke boolean
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN)
            ]);
        }

        // Default nilai untuk stock jika unlimited
        if ($this->stock === '-1' || $this->stock === -1) {
            $this->merge(['stock' => -1]);
        }

        // Parse additional_info jika string
        if ($this->has('additional_info') && is_string($this->additional_info)) {
            $this->merge([
                'additional_info' => json_decode($this->additional_info, true)
            ]);
        }
    }
}
