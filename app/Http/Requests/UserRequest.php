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

        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', $uniqueEmail],
            'role' => 'required|in:guru,siswa,ortu',
            'xp' => 'nullable|integer|min:0',
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

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama wajib diisi.',
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
        ];
    }
}
