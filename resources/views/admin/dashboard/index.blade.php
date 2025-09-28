@extends('components.admin.layout.app')

@section('header', 'Dashboard MindSphere')
@section('subtitle', 'Overview Sistem Manajemen Habits & Challenges')

@section('content')

<!-- Stats Cards -->
<div class="mb-6">
    <p class="text-gray-600">Data terkini {{ now()->translatedFormat('F Y') }}</p>
</div>


@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
    {{ session('error') }}
</div>
@endif

@if($errors->any())
<div class="p-4 mb-6 bg-red-50 border border-red-200 rounded-lg">
    <div class="flex items-center mb-2">
        <i class="mr-2 text-red-500 fa-solid fa-circle-exclamation"></i>
        <h3 class="text-sm font-semibold text-red-800">Terjadi kesalahan:</h3>
    </div>
    <ul class="ml-6 text-sm text-red-700 list-disc">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
    <!-- Total Pengguna -->
    <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Pengguna Aktif</p>

                <p class="text-3xl font-bold text-gray-900">{{ number_format($totalActiveUsers) }}</p>
                <div class="flex gap-2 mt-1 text-xs text-gray-500">
                    <span>{{ $totalStudents }} Siswa</span>
                    <span>•</span>
                    <span>{{ $totalTeachers }} Guru</span>
                    <span>•</span>
                    <span>{{ $totalParents }} Ortu</span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                <i class="text-xl text-blue-600 fa fa-users"></i>
            </div>
        </div>
    </div>

    <!-- Challenge Aktif -->
    <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Challenge Aktif</p>
                <p class="text-3xl font-bold text-gray-900">{{ $activeIndividualChallenges + $activeGroupChallenges }}
                </p>
                <div class="flex gap-2 mt-1 text-xs text-gray-500">
                    <span>{{ $activeIndividualChallenges }} Individu</span>
                    <span>•</span>
                    <span>{{ $activeGroupChallenges }} Grup</span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                <i class="text-xl text-green-600 fa fa-flag-checkered"></i>
            </div>
        </div>
    </div>

    <!-- Habit Tracker -->
    <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Habit Minggu Ini</p>
                <p class="text-3xl font-bold text-gray-900">{{ $doneHabits + $notDoneHabits }}</p>
                <div class="flex gap-2 mt-1 text-xs">
                    <span class="text-green-600">{{ $doneHabits }} Done</span>
                    <span>•</span>
                    <span class="text-red-600">{{ $notDoneHabits }} Missed</span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg">
                <i class="text-xl text-purple-600 fa fa-sync-alt"></i>
            </div>
        </div>
    </div>

    <!-- Aktivitas Hari Ini -->
    <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Refleksi Hari Ini</p>
                <p class="text-3xl font-bold text-gray-900">{{ $reflectionsToday }}</p>
                <div class="flex gap-2 mt-1 text-xs text-gray-500">
                    <span>{{ $forumPostsThisWeek }} Post Forum</span>
                    <span>•</span>
                    <span>{{ $forumCommentsThisWeek }} Komentar</span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-orange-100 rounded-lg">
                <i class="text-xl text-orange-600 fa fa-brain"></i>
            </div>
        </div>
    </div>
</div>

<!-- Quick Action Buttons -->
<div class="grid grid-cols-1 gap-4 mb-8 sm:grid-cols-2 lg:grid-cols-4">
    <a href="{{ route('admin.users.index') }}"
        class="p-4 text-white transition-all rounded-lg bg-[#2563EB] hover:bg-[#1E40AF] hover:shadow-md">
        <div class="flex flex-col items-center text-center">
            <div class="flex items-center justify-center w-12 h-12 mb-3  rounded-full bg-opacity-20">
                <i class="text-xl fa fa-users"></i>
            </div>
            <h3 class="mb-1 font-semibold">Kelola Pengguna</h3>
            <p class="text-sm text-blue-100">Data siswa, guru, orang tua</p>
        </div>
    </a>

    <a href="" class="p-4 text-white transition-all rounded-lg bg-[#22C55E] hover:bg-[#16A34A] hover:shadow-md">
        <div class="flex flex-col items-center text-center">
            <div class="flex items-center justify-center w-12 h-12 mb-3  rounded-full bg-opacity-20">
                <i class="text-xl fa fa-flag-checkered"></i>
            </div>
            <h3 class="mb-1 font-semibold">Buat Challenge</h3>
            <p class="text-sm text-green-100">Tantangan individu/grup</p>
        </div>
    </a>

    <a href="" class="p-4 text-white transition-all rounded-lg bg-[#8B5CF6] hover:bg-[#7C3AED] hover:shadow-md">
        <div class="flex flex-col items-center text-center">
            <div class="flex items-center justify-center w-12 h-12 mb-3  rounded-full bg-opacity-20">
                <i class="text-xl fa fa-tasks"></i>
            </div>
            <h3 class="mb-1 font-semibold">Kelola Habits</h3>
            <p class="text-sm text-purple-100">Kebiasaan harian/mingguan</p>
        </div>
    </a>

    <a href="" class="p-4 text-white transition-all rounded-lg bg-[#F59E0B] hover:bg-[#D97706] hover:shadow-md">
        <div class="flex flex-col items-center text-center">
            <div class="flex items-center justify-center w-12 h-12 mb-3  rounded-full bg-opacity-20">
                <i class="text-xl fa fa-award"></i>
            </div>
            <h3 class="mb-1 font-semibold">Kelola Badges</h3>
            <p class="text-sm text-yellow-100">Penghargaan & achievement</p>
        </div>
    </a>
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
            <div class="space-y-4">
                @foreach($topStudents as $index => $student)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white bg-blue-600 rounded-full">
                            {{ $index + 1 }}
                        </div>
                        @if($student['avatar_url'])
                        <img src="{{ $student['avatar_url'] }}" alt="{{ $student['name'] }}"
                            class="w-8 h-8 rounded-full object-cover">
                        @else
                        <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full">
                            <span class="text-sm font-bold text-blue-600">{{ substr($student['name'], 0, 1) }}</span>
                        </div>
                        @endif
                        <div>
                            <p class="font-medium text-gray-800">{{ $student['name'] }}</p>
                            <p class="text-sm text-gray-500">Level {{ $student['level'] }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-900">{{ number_format($student['xp']) }} XP</p>
                    </div>
                </div>
                @endforeach
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
            <div class="space-y-4">
                @forelse($recentActivities as $activity)
                <div class="flex items-start space-x-3">
                    <div class="flex items-center justify-center w-8 h-8 mt-1 rounded-full
                        @if($activity['type'] === 'challenge_completion') bg-green-100 text-green-600
                        @elseif($activity['type'] === 'badge_award') bg-yellow-100 text-yellow-600
                        @elseif($activity['type'] === 'new_challenge') bg-blue-100 text-blue-600
                        @elseif($activity['type'] === 'appreciation') bg-purple-100 text-purple-600
                        @else bg-gray-100 text-gray-600 @endif">
                        <i class="text-sm fa
                            @if($activity['type'] === 'challenge_completion') fa-flag-checkered
                            @elseif($activity['type'] === 'badge_award') fa-award
                            @elseif($activity['type'] === 'new_challenge') fa-plus-circle
                            @elseif($activity['type'] === 'appreciation') fa-heart
                            @else fa-info-circle @endif"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-800">{{ $activity['message'] }}</p>
                        <p class="text-xs text-gray-500">{{ $activity['timestamp']->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center text-gray-500 py-4">
                    <i class="mb-2 text-4xl fa fa-inbox"></i>
                    <p>Tidak ada aktivitas terbaru</p>
                </div>
                @endforelse
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
<script src="https://cdn.jsdelivr.net/npm/chart.js  "></script>
<script>
    // Chart.js Configuration
    Chart.defaults.font.family = "Inter, system-ui, sans-serif";
    Chart.defaults.color = "#6B7280";

    // Mood Distribution Chart
    const moodCtx = document.getElementById('moodChart').getContext('2d');
    const moodChart = new Chart(moodCtx, {
        type: 'doughnut',
        data: {
            labels: ['Happy', 'Neutral', 'Sad', 'Angry', 'Tired'],
            datasets: [{
                data: [
                    {{ $moodDistribution['happy'] ?? 0 }},
                    {{ $moodDistribution['neutral'] ?? 0 }},
                    {{ $moodDistribution['sad'] ?? 0 }},
                    {{ $moodDistribution['angry'] ?? 0 }},
                    {{ $moodDistribution['tired'] ?? 0 }}
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
    const habitChart = new Chart(habitCtx, {
        type: 'line',
        data: {
            labels: @json(array_column($habitTrends, 'week')),
            datasets: [
                {
                    label: 'Habit Done',
                    data: @json(array_column($habitTrends, 'done')),
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Habit Not Done',
                    data: @json(array_column($habitTrends, 'not_done')),
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
</script>
@endpush

@endsection