<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRewardRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rewardId = $this->route('reward'); // reward parameter dari route

        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'coin_cost' => 'sometimes|required|integer|min:1',
            'stock' => 'sometimes|required|integer|min:-1',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_url' => 'nullable|url',
            'remove_image' => 'boolean',
            'type' => ['sometimes', 'required', Rule::in(['physical', 'digital', 'voucher'])],
            'validity_days' => 'nullable|integer|min:1',
            'additional_info' => 'nullable|array',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama reward wajib diisi',
            'name.max' => 'Nama reward maksimal 255 karakter',
            'coin_cost.min' => 'Biaya koin minimal 1',
            'stock.min' => 'Stok minimal -1 (unlimited)',
            'image.image' => 'File harus berupa gambar',
            'image.mimes' => 'Format gambar harus: jpeg, png, jpg, gif',
            'image.max' => 'Ukuran gambar maksimal 2MB',
            'remove_image.boolean' => 'Hapus gambar harus benar atau salah',
        ];
    }

    public function prepareForValidation()
    {
        // Konversi string boolean ke boolean
        $booleans = ['is_active', 'remove_image'];
        foreach ($booleans as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->$field, FILTER_VALIDATE_BOOLEAN)
                ]);
            }
        }

        // Default nilai untuk stock jika unlimited
        if ($this->has('stock') && ($this->stock === '-1' || $this->stock === -1)) {
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
