@extends('components.admin.layout.app')

@section('header', 'Dashboard MindSphere')
@section('subtitle', 'Overview Sistem Manajemen Habits & Challenges')

@section('content')
<!-- Stats Cards -->
<div class="mb-6">
    <p class="text-gray-600">Data terkini {{ now()->translatedFormat('F Y') }}</p>
</div>

<div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
    <!-- Total Pengguna -->
    <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Pengguna Aktif</p>
                <p class="text-3xl font-bold text-gray-900" id="totalActiveUsers">0</p>
                <div class="flex gap-2 mt-1 text-xs text-gray-500">
                    <span id="totalStudents">0</span> Siswa
                    <span>•</span>
                    <span id="totalTeachers">0</span> Guru
                    <span>•</span>
                    <span id="totalParents">0</span> Ortu
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                <i class="text-xl text-blue-600 fas fa-users"></i>
            </div>
        </div>
    </div>

    <!-- Challenge Aktif -->
    <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Challenge Aktif</p>
                <p class="text-3xl font-bold text-gray-900" id="totalActiveChallenges">0</p>
                <div class="flex gap-2 mt-1 text-xs text-gray-500">
                    <span id="activeIndividualChallenges">0</span> Individu
                    <span>•</span>
                    <span id="activeGroupChallenges">0</span> Grup
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                <i class="text-xl text-green-600 fas fa-flag-checkered"></i>
            </div>
        </div>
    </div>

    <!-- Habit Tracker -->
    <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Habit Minggu Ini</p>
                <p class="text-3xl font-bold text-gray-900" id="totalHabits">0</p>
                <div class="flex gap-2 mt-1 text-xs">
                    <span class="text-green-600" id="doneHabits">0</span> Done
                    <span>•</span>
                    <span class="text-red-600" id="notDoneHabits">0</span> Missed
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg">
                <i class="text-xl text-purple-600 fas fa-sync-alt"></i>
            </div>
        </div>
    </div>

    <!-- Aktivitas Hari Ini -->
    <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Refleksi Hari Ini</p>
                <p class="text-3xl font-bold text-gray-900" id="reflectionsToday">0</p>
                <div class="flex gap-2 mt-1 text-xs text-gray-500">
                    <span id="forumPostsThisWeek">0</span> Post Forum
                    <span>•</span>
                    <span id="forumCommentsThisWeek">0</span> Komentar
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-orange-100 rounded-lg">
                <i class="text-xl text-orange-600 fas fa-brain"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <!-- Leaderboard & Top Performers -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-soft">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Top 10 Leaderboard</h3>
            <p class="text-sm text-gray-500">Siswa dengan XP tertinggi</p>
        </div>
        <div class="p-6">
            <div class="space-y-4" id="topStudentsContainer">
                <p class="text-center text-gray-500">Memuat data...</p>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-soft">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Aktivitas Terbaru</h3>
            <p class="text-sm text-gray-500">Update terbaru dari sistem</p>
        </div>
        <div class="p-6">
            <div class="space-y-4" id="recentActivitiesContainer">
                <p class="text-center text-gray-500">Memuat data...</p>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 gap-6 mt-6 lg:grid-cols-2">
    <!-- Mood Distribution -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-soft">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Distribusi Mood Siswa</h3>
            <p class="text-sm text-gray-500">Minggu terakhir</p>
        </div>
        <div class="p-6">
            <canvas id="moodChart" width="400" height="250"></canvas>
        </div>
    </div>

    <!-- Habit Trends -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-soft">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Tren Habit Completion</h3>
            <p class="text-sm text-gray-500">5 minggu terakhir</p>
        </div>
        <div class="p-6">
            <canvas id="habitTrendChart" width="400" height="250"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const API_URL = 'http://127.0.0.1:8080/api/v1/admin/dashboard';

    async function loadDashboardData() {
        try {
            const response = await fetch(API_URL);
            const result = await response.json();
            const data = result.data;

            // Update stats cards
            document.getElementById('totalActiveUsers').textContent = data.total_active_users;
            document.getElementById('totalStudents').textContent = data.total_students;
            document.getElementById('totalTeachers').textContent = data.total_teachers;
            document.getElementById('totalParents').textContent = data.total_parents;
            document.getElementById('activeIndividualChallenges').textContent = data.active_individual_challenges;
            document.getElementById('activeGroupChallenges').textContent = data.active_group_challenges;
            document.getElementById('totalActiveChallenges').textContent = data.active_individual_challenges + data.active_group_challenges;
            document.getElementById('doneHabits').textContent = data.done_habits;
            document.getElementById('notDoneHabits').textContent = data.not_done_habits;
            document.getElementById('totalHabits').textContent = data.done_habits + data.not_done_habits;
            document.getElementById('reflectionsToday').textContent = data.reflections_today;
            document.getElementById('forumPostsThisWeek').textContent = data.forum_posts_this_week;
            document.getElementById('forumCommentsThisWeek').textContent = data.forum_comments_this_week;

            // Update leaderboard
            const topStudentsContainer = document.getElementById('topStudentsContainer');
            topStudentsContainer.innerHTML = '';
            if (data.top_students && data.top_students.length > 0) {
                // Hapus dummy data jika nama adalah "Belum Ada Data"
                const validStudents = data.top_students.filter(s => s.name !== "Belum Ada Data");
                if (validStudents.length > 0) {
                    validStudents.forEach((student, index) => {
                        topStudentsContainer.innerHTML += `
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white bg-blue-600 rounded-full">
                                        ${index + 1}
                                    </div>
                                    <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full">
                                        <span class="text-lg font-bold text-blue-600">${student.name.charAt(0)}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">${student.name}</p>
                                        <p class="text-sm text-gray-500">Level ${student.level}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900">${(student.xp || 0).toLocaleString()} XP</p>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    topStudentsContainer.innerHTML = '<p class="text-center text-gray-500">Belum ada data siswa</p>';
                }
            } else {
                topStudentsContainer.innerHTML = '<p class="text-center text-gray-500">Belum ada data siswa</p>';
            }
            
            // Update recent activities
            const recentActivitiesContainer = document.getElementById('recentActivitiesContainer');
            recentActivitiesContainer.innerHTML = '';
            if (data.recent_activities && data.recent_activities.length > 0) {
                const validActivities = data.recent_activities.filter(a => a.message !== "Belum ada aktivitas terbaru");
                if (validActivities.length > 0) {
                    validActivities.forEach(activity => {
                        // ... render activity
                    });
                } else {
                    recentActivitiesContainer.innerHTML = `
                        <div class="text-center text-gray-500 py-4">
                            <i class="mb-2 text-4xl fas fa-inbox"></i>
                            <p>Belum ada aktivitas terbaru</p>
                        </div>
                    `;
                }
            } else {
                recentActivitiesContainer.innerHTML = `
                    <div class="text-center text-gray-500 py-4">
                        <i class="mb-2 text-4xl fas fa-inbox"></i>
                        <p>Belum ada aktivitas terbaru</p>
                    </div>
                `;
            }

            // Initialize charts
            initCharts(data);

        } catch (error) {
            console.error('Error loading dashboard data:', error);
            alert('Gagal memuat data dashboard.');
        }
    }

    function initCharts(data) {
        // Mood Distribution Chart
        const moodCtx = document.getElementById('moodChart').getContext('2d');
        new Chart(moodCtx, {
            type: 'doughnut',
            data: {
                labels: ['Happy', 'Neutral', 'Sad', 'Angry', 'Tired'],
                datasets: [{
                    data: [
                        data.mood_distribution.happy || 0,
                        data.mood_distribution.neutral || 0,
                        data.mood_distribution.sad || 0,
                        data.mood_distribution.angry || 0,
                        data.mood_distribution.tired || 0
                    ],
                    backgroundColor: [
                        '#10B981', '#6B7280', '#3B82F6', '#EF4444', '#F59E0B'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Habit Trends Chart
        const habitCtx = document.getElementById('habitTrendChart').getContext('2d');
        new Chart(habitCtx, {
            type: 'line',
            data: {
                labels: data.habit_trends.map(t => t.week),
                datasets: [
                    {
                        label: 'Habit Done',
                        data: data.habit_trends.map(t => t.done),
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Habit Not Done',
                        data: data.habit_trends.map(t => t.not_done),
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Load data when page loads
    document.addEventListener('DOMContentLoaded', loadDashboardData);
</script>
@endpush

@endsection
