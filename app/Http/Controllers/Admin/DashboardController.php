<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    private string $goApiUrl;

    public function __construct()
    {
        $this->goApiUrl = config('services.go_admin.url', 'http://localhost:8080');
    }

    public function index()
    {
        $response = Http::get("{$this->goApiUrl}/api/v1/admin/dashboard");

        if (!$response->successful()) {
            // Jika API Go gagal, kirim data default
            return view('admin.dashboard.index', $this->getDefaultData());
        }

        $data = $response->json()['data'];

        // Format habit trends untuk memastikan konsistensi
        $habitTrends = $this->formatHabitTrends($data['habit_trends'] ?? []);

        // Pastikan semua key ada di data
        return view('admin.dashboard.index', [
            'totalActiveUsers' => $data['total_active_users'] ?? 0,
            'totalStudents' => $data['total_students'] ?? 0,
            'totalTeachers' => $data['total_teachers'] ?? 0,
            'totalParents' => $data['total_parents'] ?? 0,
            'activeIndividualChallenges' => $data['active_individual_challenges'] ?? 0,
            'activeGroupChallenges' => $data['active_group_challenges'] ?? 0,
            'doneHabits' => $data['done_habits'] ?? 0,
            'notDoneHabits' => $data['not_done_habits'] ?? 0,
            'reflectionsToday' => $data['reflections_today'] ?? 0,
            'forumPostsThisWeek' => $data['forum_posts_this_week'] ?? 0,
            'forumCommentsThisWeek' => $data['forum_comments_this_week'] ?? 0,
            'topStudents' => collect($data['top_students'] ?? []),
            'recentActivities' => collect($data['recent_activities'] ?? []),
            'moodDistribution' => $data['mood_distribution'] ?? [],
            'habitTrends' => $habitTrends,
        ]);
    }

    /**
     * Format habit trends data untuk memastikan struktur yang konsisten
     */
    private function formatHabitTrends(array $habitTrends): array
    {
        // Jika data kosong, buat data dummy untuk testing
        if (empty($habitTrends)) {
            return $this->getDefaultHabitTrends();
        }

        // Pastikan setiap item memiliki struktur yang benar
        $formatted = [];
        foreach ($habitTrends as $trend) {
            $formatted[] = [
                'week' => $trend['week'] ?? 'Minggu',
                'done' => (int) ($trend['done'] ?? 0),
                'not_done' => (int) ($trend['not_done'] ?? 0),
            ];
        }

        return $formatted;
    }

    /**
     * Data default untuk habit trends (5 minggu terakhir)
     */
    private function getDefaultHabitTrends(): array
    {
        $weeks = [];
        $today = now();

        for ($i = 4; $i >= 0; $i--) {
            $startWeek = $today->copy()->subWeeks($i)->startOfWeek();
            $endWeek = $startWeek->copy()->endOfWeek();

            $weeks[] = [
                'week' => $startWeek->format('M j') . ' - ' . $endWeek->format('M j'),
                'done' => rand(50, 100),
                'not_done' => rand(10, 40),
            ];
        }

        return $weeks;
    }

    private function getDefaultData(): array
    {
        return [
            'totalActiveUsers' => 0,
            'totalStudents' => 0,
            'totalTeachers' => 0,
            'totalParents' => 0,
            'activeIndividualChallenges' => 0,
            'activeGroupChallenges' => 0,
            'doneHabits' => 0,
            'notDoneHabits' => 0,
            'reflectionsToday' => 0,
            'forumPostsThisWeek' => 0,
            'forumCommentsThisWeek' => 0,
            'topStudents' => collect([]),
            'recentActivities' => collect([]),
            'moodDistribution' => [
                'angry' => 0,
                'happy' => 0,
                'neutral' => 0,
                'sad' => 0,
                'tired' => 0,
            ],
            'habitTrends' => $this->getDefaultHabitTrends(),
        ];
    }
}
