<?php

namespace App\Http\Services;

use App\Enums\Role;
use App\Models\ChallengeParticipant;
use App\Models\HabitLog;
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
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
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
        return User::with([
            'challenges' => function ($query) {
                $query->with('category');
            },
            'habitsAssigned' => function ($query) {
                $query->with('category');
            },
            'classAsStudent' => function ($query) {
                $query->with('teacher');
            }
        ])->findOrFail($id);
    }

    public function getUserWithProgress($id)
    {
        $user = $this->getUserById($id);

        // Get challenge progress
        $challengeProgress = ChallengeParticipant::where('user_id', $id)
            ->with([
                'challenge' => function ($query) {
                    $query->with('category');
                }
            ])
            ->get()
            ->map(function ($participant) {
                return [
                    'id' => $participant->challenge->id,
                    'title' => $participant->challenge->title,
                    'category' => $participant->challenge->category->name,
                    'xp_reward' => $participant->challenge->xp_reward,
                    'status' => $participant->status->value,
                    'proof_url' => $participant->proof_url,
                    'submitted_at' => $participant->submitted_at,
                    'joined_at' => $participant->created_at,
                    'start_date' => $participant->challenge->start_date,
                    'end_date' => $participant->challenge->end_date,
                ];
            });

        // Get habit progress
        $habitProgress = HabitLog::where('user_id', $id)
            ->with([
                'habit' => function ($query) {
                    $query->with('category');
                }
            ])
            ->get()
            ->groupBy('habit_id')
            ->map(function ($logs, $habitId) {
                $habit = $logs->first()->habit;
                $completedCount = $logs->where('status', 'completed')->count();
                $totalDays = $logs->count();

                return [
                    'id' => $habit->id,
                    'title' => $habit->title,
                    'category' => $habit->category->name,
                    'xp_reward' => $habit->xp_reward,
                    'period' => $habit->period,
                    'total_logs' => $totalDays,
                    'completed_logs' => $completedCount,
                    'completion_rate' => $totalDays > 0 ? round(($completedCount / $totalDays) * 100) : 0,
                    'last_activity' => $logs->sortByDesc('date')->first()->date ?? null,
                ];
            })->values();

        // Get statistics
        $stats = [
            'total_challenges' => $challengeProgress->count(),
            'completed_challenges' => $challengeProgress->where('status', 'completed')->count(),
            'active_challenges' => $challengeProgress->whereIn('status', ['joined', 'submitted'])->count(),
            'total_habits' => $habitProgress->count(),
            'total_xp_earned' => $challengeProgress->where('status', 'completed')->sum('xp_reward') +
                $habitProgress->sum(function ($habit) {
                    return $habit['completed_logs'] * $habit['xp_reward'];
                }),
        ];

        return [
            'user' => $user,
            'challenge_progress' => $challengeProgress,
            'habit_progress' => $habitProgress,
            'stats' => $stats
        ];
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
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'],
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
        try {
            $user = User::findOrFail($id);

            // Jika role bukan siswa, set parent_id ke null
            if ($data['role'] !== 'siswa') {
                $data['parent_id'] = null;
            }

            // Jika password kosong, hapus dari data
            if (empty($data['password'])) {
                unset($data['password']);
            } else {
                $data['password'] = bcrypt($data['password']);
            }

            $user->update($data);

            return $user;
        } catch (\Exception $e) {
            throw new \Exception('Gagal memperbarui user: ' . $e->getMessage());
        }
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
