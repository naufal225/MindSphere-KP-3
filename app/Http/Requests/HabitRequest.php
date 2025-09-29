<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HabitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            // 'type'         => ['required', Rule::in(['self', 'assigned'])],
            'assigned_by'  => $this->isTypeAssigned() ? 'required|exists:users,id' : 'nullable|exists:users,id',
            'category_id'  => 'required|exists:categories,id',
            'period'       => ['required', Rule::in(['daily', 'weekly'])],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'Judul kebiasaan wajib diisi.',
            'title.string'         => 'Judul kebiasaan harus berupa teks.',
            'title.max'            => 'Judul kebiasaan maksimal 255 karakter.',

            'description.required' => 'Deskripsi kebiasaan wajib diisi.',
            'description.string'   => 'Deskripsi kebiasaan harus berupa teks.',

            // 'type.required'        => 'Jenis kebiasaan wajib dipilih.',
            // 'type.in'              => 'Jenis kebiasaan tidak valid. Pilih antara "self" atau "assigned".',

            'assigned_by.required' => 'Pengguna yang menugaskan wajib diisi untuk kebiasaan bertipe "assigned".',
            'assigned_by.exists'   => 'Pengguna yang menugaskan tidak ditemukan.',

            'category_id.required' => 'Kategori kebiasaan wajib dipilih.',
            'category_id.exists'   => 'Kategori yang dipilih tidak ditemukan.',

            'period.required'      => 'Periode kebiasaan wajib dipilih.',
            'period.in'            => 'Periode kebiasaan tidak valid. Pilih antara "daily" atau "weekly".',
        ];
    }

    private function isTypeAssigned(): bool
    {
        $type = $this->input('type');
        return $type === 'assigned';
    }
}
