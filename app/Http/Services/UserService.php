<?php

namespace App\Http\Services;

use App\Enums\Role;
use App\Models\SchoolClass;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function getUsers(Request $request)
    {
        $query = User::query()->whereNot('role', Role::ADMIN);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by class
        if ($request->filled('class_id')) {
            $classId = $request->class_id;
            $query->where(function ($q) use ($classId) {
                // Untuk siswa yang berada di kelas tertentu
                $q->whereHas('classAsStudent', function ($studentQuery) use ($classId) {
                    $studentQuery->where('class_id', $classId);
                })
                    // Untuk guru yang mengajar kelas tertentu
                    ->orWhereHas('classesAsTeacher', function ($teacherQuery) use ($classId) {
                        $teacherQuery->where('id', $classId);
                    });
            });
        }
        
        return $query->orderBy('name')->paginate(10)->withQueryString();
    }

    public function getUserById($id)
    {
        return User::findOrFail($id);
    }

    public function createUser(array $data)
    {
        try {
            DB::beginTransaction();

            $avatarUrl = null;
            if (isset($data['avatar_file']) && $data['avatar_file'] instanceof UploadedFile) {
                $avatarUrl = $this->uploadAvatar($data['avatar_file']);
            }

            $data = collect($data)->except(['avatar_file'])->toArray();
            $data['password'] = bcrypt($data['password'] ?? 'password');
            $data['avatar_url'] = $avatarUrl ?? $data['avatar_url'] ?? null;

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
                'xp' => $data['xp'] ?? 0,
                'level' => $data['level'] ?? 1,
                'avatar_url' => $data['avatar_url'] ?? null,
            ]);

            // Handle assignment berdasarkan role
            if ($data['role'] === 'guru' && isset($data['class_id'])) {
                SchoolClass::where('id', $data['class_id'])
                    ->update(['teacher_id' => $user->id]);
            } elseif ($data['role'] === 'siswa' && isset($data['class_id'])) {
                $user->classAsStudent()->attach($data['class_id']);
            }

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Gagal membuat user: " . $e->getMessage());
        }
    }

    public function updateUser($id, array $data)
    {
        $user = User::findOrFail($id);

        if (isset($data['avatar_file']) && $data['avatar_file'] instanceof UploadedFile) {
            // Hapus avatar lama jika ada
            if ($user->avatar_url) {
                $this->deleteAvatar($user->avatar_url);
            }

            $data['avatar_url'] = $this->uploadAvatar($data['avatar_file']);
            unset($data['avatar_file']);
        }

        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        // Handle perubahan role & kelas
        $this->handleRoleAndClassAssignment($user, $data);

        $user->update($data);
        return $user;
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Hapus avatar jika ada
        if ($user->avatar_url) {
            $this->deleteAvatar($user->avatar_url);
        }

        $user->delete();
    }

    private function uploadAvatar(UploadedFile $file): string
    {
        $path = $file->store('avatars', 'public');
        return Storage::url($path);
    }

    private function deleteAvatar(string $avatarUrl): void
    {
        $path = str_replace('/storage/', 'public/', $avatarUrl);
        if (Storage::exists($path)) {
            Storage::delete($path);
        }
    }

    private function handleRoleAndClassAssignment(User $user, array $data)
    {
        // Jika role berubah dari guru → non-guru, lepas dari semua kelas sebagai wali
        if ($user->wasRecentlyCreated === false && $user->role === 'guru' && $data['role'] !== 'guru') {
            SchoolClass::where('teacher_id', $user->id)->update(['teacher_id' => null]);
        }

        // Jika role berubah dari siswa → non-siswa, lepas dari semua kelas sebagai siswa
        if ($user->wasRecentlyCreated === false && $user->role === 'siswa' && $data['role'] !== 'siswa') {
            $user->classAsStudent()->detach();
        }

        // Update role
        $user->role = $data['role'];
        $user->save();

        // Assign ke kelas baru berdasarkan role
        if ($data['role'] === 'guru' && isset($data['class_id'])) {
            // Lepaskan guru lama dari kelas ini
            SchoolClass::where('id', $data['class_id'])->update(['teacher_id' => $user->id]);
        } elseif ($data['role'] === 'siswa' && isset($data['class_id'])) {
            // Ganti kelas siswa
            $user->classAsStudent()->sync([$data['class_id']]);
        }
    }
}
