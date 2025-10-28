<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Reflection;
use App\Enums\Mood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ReflectionSubmissionController extends Controller
{
    public function getTodayReflection()
    {
        $user = Auth::user();

        $reflection = Reflection::with(['category'])
            ->where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->first();

        if (!$reflection) {
            return response()->json([
                'status' => 'success',
                'data' => null
            ]);
        }

        $reflectionData = [
            'id' => $reflection->id,
            'mood' => $reflection->mood->value,
            'content' => $reflection->content,
            'category' => optional($reflection->category)->name,
            'category_id' => $reflection->category_id,
            'is_private' => $reflection->is_private,
            'date' => $reflection->date->format('Y-m-d'),
            'created_at' => $reflection->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $reflection->updated_at->format('Y-m-d H:i:s'),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $reflectionData
        ]);
    }

    public function submitMood(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'mood' => 'required|in:happy,neutral,sad,angry,tired',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $date = Carbon::parse($request->date);

            // Cek apakah sudah ada refleksi untuk tanggal tersebut
            $existingReflection = Reflection::where('user_id', $user->id)
                ->whereDate('date', $date)
                ->first();

            if ($existingReflection) {
                // Update mood existing reflection
                $existingReflection->update([
                    'mood' => Mood::from($request->mood)
                ]);

                $reflection = $existingReflection;
            } else {
                // Create new reflection with mood only
                $reflection = Reflection::create([
                    'user_id' => $user->id,
                    'mood' => Mood::from($request->mood),
                    'content' => '', // Empty content for mood-only submission
                    'is_private' => true,
                    'date' => $date,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Mood berhasil disimpan',
                'data' => [
                    'id' => $reflection->id,
                    'mood' => $reflection->mood->value,
                    'date' => $reflection->date->format('Y-m-d'),
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan mood',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getMonthlyMoods($year, $month)
    {
        $user = Auth::user();

        $moods = Reflection::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($reflection) {
                return [
                    'date' => $reflection->date->format('Y-m-d'),
                    'mood' => $reflection->mood->value,
                    'has_content' => !empty($reflection->content),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $moods
        ]);
    }

    public function getStats()
    {
        $user = Auth::user();

        // Get mood distribution for current month
        $currentMonthMoods = Reflection::where('user_id', $user->id)
            ->whereYear('date', Carbon::now()->year)
            ->whereMonth('date', Carbon::now()->month)
            ->get();

        $moodDistribution = $currentMonthMoods->groupBy(function($reflection) {
            return $reflection->mood->value;
        })->map(function($group) {
            return $group->count();
        });

        // Get total reflections count
        $totalReflections = Reflection::where('user_id', $user->id)->count();

        // Get reflections with content count
        $reflectionsWithContent = Reflection::where('user_id', $user->id)
            ->where('content', '!=', '')
            ->count();

        // Get current streak
        $streak = $this->calculateStreak($user->id);

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_reflections' => $totalReflections,
                'reflections_with_content' => $reflectionsWithContent,
                'current_streak' => $streak,
                'mood_distribution' => $moodDistribution,
            ]
        ]);
    }

    private function calculateStreak($userId)
    {
        $reflections = Reflection::where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->get();

        if ($reflections->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $currentDate = Carbon::today();

        foreach ($reflections as $reflection) {
            if ($reflection->date->format('Y-m-d') === $currentDate->format('Y-m-d')) {
                $streak++;
                $currentDate->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }
}
