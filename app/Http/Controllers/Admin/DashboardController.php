<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    private const ALLOWED_RANGES = ['semua', 'minggu ini', 'bulan ini', 'tahun ini'];
    private string $goApiUrl;

    public function __construct()
    {
        $this->goApiUrl = config('services.go_admin.url', 'http://localhost:8080');
    }

    public function index(Request $request)
    {
        $selectedRange = $this->resolveRange($request->query('range'));
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = [
            'range' => $selectedRange,
        ];

        if ($startDate && $endDate) {
            $query['start_date'] = $startDate;
            $query['end_date'] = $endDate;
        }

        $response = Http::get("{$this->goApiUrl}/api/v1/admin/dashboard", $query);

        if (!$response->successful()) {
            // Jika API Go gagal, kirim data default
            return view('admin.dashboard.index', $this->getDefaultData($selectedRange));
        }

        $data = $response->json('data') ?? [];

        // Format habit/challenge trends untuk memastikan konsistensi
        $habitTrends = $this->formatProgress($data['habit_trends'] ?? []);
        $challengeProgress = $this->formatProgress($data['challenge_progress'] ?? []);

        // Pastikan semua key ada di data
        return view('admin.dashboard.index', [
            'selectedRange' => $selectedRange,
            'totalActiveUsers' => $data['total_active_users'] ?? 0,
            'totalStudents' => $data['total_students'] ?? 0,
            'totalTeachers' => $data['total_teachers'] ?? 0,
            'totalParents' => $data['total_parents'] ?? 0,
            'activeChallenges' => $data['active_challenges'] ?? 0,
            'activeHabits' => $data['active_habits'] ?? 0,
            'doneHabits' => $this->sumHabitTrends($habitTrends, 'done'),
            'notDoneHabits' => $this->sumHabitTrends($habitTrends, 'not_done'),
            'reflectionsToday' => $data['reflections_today'] ?? 0,
            'topStudents' => collect($data['top_students'] ?? []),
            'recentActivities' => collect($data['recent_activities'] ?? []),
            'moodDistribution' => $data['mood_distribution'] ?? [],
            'habitTrends' => $habitTrends,
            'challengeProgress' => $challengeProgress,
        ]);
    }

    /**
     * Format progress data untuk memastikan struktur yang konsisten
     */
    private function formatProgress(array $progress): array
    {
        if (empty($progress)) {
            return [];
        }

        return collect($progress)->map(function ($trend) {
            return [
                'week' => $trend['week'] ?? 'Periode',
                'done' => (int) ($trend['done'] ?? 0),
                'not_done' => (int) ($trend['not_done'] ?? 0),
            ];
        })->toArray();
    }

    private function getDefaultData(string $selectedRange): array
    {
        return [
            'selectedRange' => $selectedRange,
            'totalActiveUsers' => 0,
            'totalStudents' => 0,
            'totalTeachers' => 0,
            'totalParents' => 0,
            'activeChallenges' => 0,
            'activeHabits' => 0,
            'doneHabits' => 0,
            'notDoneHabits' => 0,
            'reflectionsToday' => 0,
            'topStudents' => collect([]),
            'recentActivities' => collect([]),
            'moodDistribution' => [
                'angry' => 0,
                'happy' => 0,
                'neutral' => 0,
                'sad' => 0,
                'tired' => 0,
            ],
            'habitTrends' => [],
            'challengeProgress' => [],
        ];
    }

    private function resolveRange(?string $range): string
    {
        $normalizedRange = strtolower(trim($range ?? ''));

        return in_array($normalizedRange, self::ALLOWED_RANGES, true)
            ? $normalizedRange
            : 'semua';
    }

    private function sumHabitTrends(array $habitTrends, string $key): int
    {
        return collect($habitTrends)->sum(function ($trend) use ($key) {
            return (int) ($trend[$key] ?? 0);
        });
    }
}
