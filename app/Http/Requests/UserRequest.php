<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userId = $this->route('user');
        $uniqueEmail = $userId ? 'unique:users,email,' . $userId : 'unique:users,email';
        $uniqueUsername = $userId ? 'unique:users,username,' . $userId : 'unique:users,username';

        $rules = [
            'name' => 'required|string|max:255',
            'username' => ['required', 'alpha_dash', 'min:3', 'max:30', $uniqueUsername],
            'email' => ['required', 'email', $uniqueEmail],
            'role' => 'required|in:guru,siswa,ortu',
            'parent_id' => 'nullable|exists:users,id',
            'xp' => 'nullable|integer|min:0',
            'class_id' => 'nullable|exists:school_classes,id',
            'level' => 'nullable|integer|min:1',
            'avatar_url' => 'nullable|url',
        ];

        // Tambahkan validasi untuk avatar_file jika ada
        if ($this->hasFile('avatar_file')) {
            $rules['avatar_file'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        if ($this->isMethod('post')) { // Create
            $rules['password'] = 'required|string|min:6';
        } else { // Update
            $rules['password'] = 'nullable|string|min:6';
        }

        // Validasi conditional untuk parent_id
        if ($this->input('role') === 'siswa') {
            $rules['parent_id'] = 'nullable|exists:users,id,role,ortu';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, strip dan underscore.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.max' => 'Username maksimal 30 karakter.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'avatar_file.image' => 'File harus berupa gambar.',
            'avatar_file.mimes' => 'Format gambar harus: jpeg, png, jpg, gif.',
            'avatar_file.max' => 'Ukuran gambar maksimal 2MB.',
            'parent_id.exists' => 'Orang tua yang dipilih tidak valid atau bukan role orang tua.',
        ];
    }
}
