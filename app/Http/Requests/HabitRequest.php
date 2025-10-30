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
            'category_id'  => 'required|exists:categories,id',
            'period'       => ['required', Rule::in(['daily', 'weekly'])],
            'xp_reward'    => 'required|integer|min:1|max:10000',
            'start_date'   => 'required|date|after_or_equal:today',
            'end_date'     => 'required|date|after_or_equal:start_date',
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

            'category_id.required' => 'Kategori kebiasaan wajib dipilih.',
            'category_id.exists'   => 'Kategori yang dipilih tidak ditemukan.',

            'period.required'      => 'Periode kebiasaan wajib dipilih.',
            'period.in'            => 'Periode kebiasaan tidak valid. Pilih antara "daily" atau "weekly".',

            'xp_reward.required'   => 'XP reward wajib diisi.',
            'xp_reward.integer'    => 'XP reward harus berupa angka.',
            'xp_reward.min'        => 'XP reward minimal 1.',
            'xp_reward.max'        => 'XP reward maksimal 10000.',

            'start_date.required'  => 'Tanggal mulai wajib diisi.',
            'start_date.date'      => 'Format tanggal mulai tidak valid.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh kurang dari hari ini.',

            'end_date.required'    => 'Tanggal berakhir wajib diisi.',
            'end_date.date'        => 'Format tanggal berakhir tidak valid.',
            'end_date.after_or_equal'  => 'Tanggal berakhir tidak boleh kurang dari tanggal mulai.',
        ];
    }

}
