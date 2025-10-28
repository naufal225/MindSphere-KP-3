<?php

namespace App\Http\Controllers\Api\Ortu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $parent = Auth::user();

            // Validasi bahwa user adalah orang tua
            if ($parent->role !== 'ortu') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Hanya untuk role orang tua.'
                ], 403);
            }

            // Ambil data anak-anak beserta informasi kelas
            $children = User::with(['classAsStudent'])
                ->where('parent_id', $parent->id)
                ->where('role', 'siswa')
                ->get()
                ->map(function($child) {
                    // Hitung aktivitas minggu ini
                    $activityPercentage = $this->calculateWeeklyActivity($child->id);

                    // Ambil nama kelas (jika ada)
                    $className = 'Belum ada kelas';
                    if ($child->classAsStudent->isNotEmpty()) {
                        $class = $child->classAsStudent->first();
                        $className = $class->name; // Contoh: "X RPL 1"
                    }

                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'email' => $child->email,
                        'level' => $child->level,
                        'xp' => $child->xp,
                        'avatar_url' => $child->avatar_url,
                        'class' => $className,
                        'weekly_activity' => $activityPercentage,
                        'last_activity' => $child->updated_at->format('Y-m-d H:i:s')
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'parent' => [
                        'id' => $parent->id,
                        'name' => $parent->name,
                        'email' => $parent->email,
                        'avatar_url' => $parent->avatar_url
                    ],
                    'children' => $children,
                    'summary' => [
                        'total_children' => $children->count(),
                        'average_activity' => $children->avg('weekly_activity') ?? 0,
                        'total_xp' => $children->sum('xp')
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghitung persentase aktivitas minggu ini
     */
    private function calculateWeeklyActivity($studentId)
    {
        try {
            // Hitung mulai dari 7 hari yang lalu
            $startOfWeek = now()->subDays(7)->startOfDay();
            $endOfWeek = now()->endOfDay();

            // Hitung total habit logs dalam 7 hari terakhir
            $totalHabitLogs = DB::table('habit_logs')
                ->where('user_id', $studentId)
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->count();

            // Hitung total habits yang seharusnya dikerjakan (asumsi 3 habits per hari)
            $expectedHabits = 3 * 7; // 3 habits x 7 hari

            // Hitung persentase (maksimal 100%)
            $percentage = $expectedHabits > 0 ? min(($totalHabitLogs / $expectedHabits) * 100, 100) : 0;

            return round($percentage);

        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get detail anak tertentu
     */
    public function getChildDetail($childId)
    {
        try {
            $parent = Auth::user();

            // Validasi bahwa anak memang milik orang tua ini
            $child = User::with([
                'classAsStudent',
                'badges' => function($query) {
                    $query->orderBy('user_badges.created_at', 'desc')->take(3);
                },
                'habitLogs' => function($query) {
                    $query->where('created_at', '>=', now()->subDays(7))
                          ->with('habit')
                          ->orderBy('created_at', 'desc');
                }
            ])
            ->where('id', $childId)
            ->where('parent_id', $parent->id)
            ->where('role', 'siswa')
            ->first();

            if (!$child) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data anak tidak ditemukan'
                ], 404);
            }

            $activityPercentage = $this->calculateWeeklyActivity($child->id);

            // Ambil nama kelas
            $className = 'Belum ada kelas';
            if ($child->classAsStudent->isNotEmpty()) {
                $class = $child->classAsStudent->first();
                $className = $class->name;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'child' => [
                        'id' => $child->id,
                        'name' => $child->name,
                        'email' => $child->email,
                        'level' => $child->level,
                        'xp' => $child->xp,
                        'avatar_url' => $child->avatar_url,
                        'class' => $className,
                    ],
                    'weekly_activity' => $activityPercentage,
                    'recent_badges' => $child->badges->map(function($badge) {
                        return [
                            'id' => $badge->id,
                            'name' => $badge->name,
                            'description' => $badge->description,
                            'icon_url' => $badge->icon_url,
                            'awarded_at' => $badge->pivot->awarded_at
                        ];
                    }),
                    'stats' => [
                        'total_habits_this_week' => $child->habitLogs->count(),
                        'total_challenges' => $child->challengeParticipants()->count(),
                        'total_reflections' => $child->reflections()->count(),
                        'total_badges' => $child->badges()->count()
                    ],
                    'weekly_habits' => $child->habitLogs->groupBy(function($log) {
                        return $log->created_at->format('Y-m-d');
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
