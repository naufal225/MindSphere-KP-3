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
        return view('admin.dashboard.index');
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
            'habitTrends' => [],
        ];
    }

}
