<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SchoolClassRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // Jika ini adalah update (ada parameter 'school_class' di route), maka dapatkan ID-nya
        $classId = $this->route('school_class');

        return [
            'name' => $classId
                ? "required|string|max:255|unique:school_classes,name,{$classId}"
                : 'required|string|max:255|unique:school_classes,name',
            'teacher_id' => 'nullable|exists:users,id,role,guru',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama kelas wajib diisi.',
            'name.string' => 'Nama kelas harus berupa teks.',
            'name.max' => 'Nama kelas tidak boleh lebih dari 255 karakter.',
            'name.unique' => 'Nama kelas sudah digunakan.',
            'teacher_id.exists' => 'Guru yang dipilih tidak valid.',
        ];
    }
}
