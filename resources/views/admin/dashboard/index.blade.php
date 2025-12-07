@extends('components.admin.layout.app')

@section('header', 'Dashboard KeepItGrow')
@section('subtitle', 'Overview Sistem Manajemen Habits & Challenges')

@section('content')

@php
    $currentRange = $selectedRange ?? 'semua';
@endphp
<!-- Stats Cards -->
<div class="flex flex-col gap-4 mb-6 md:flex-row md:items-center md:justify-between">
    <div>
        <p class="text-gray-600">Ringkasan data berdasarkan rentang waktu yang dipilih</p>
        <p class="text-sm text-gray-500">Rentang waktu: {{ ucwords($currentRange) }}</p>
    </div>
    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
        <label for="range" class="text-sm font-medium text-gray-700 whitespace-nowrap">Filter Waktu</label>
        <select name="range" id="range"
            class="px-3 py-2 text-sm border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            onchange="this.form.submit()">
            <option value="semua" {{ $currentRange === 'semua' ? 'selected' : '' }}>Semua</option>
            <option value="minggu ini" {{ $currentRange === 'minggu ini' ? 'selected' : '' }}>Minggu Ini</option>
            <option value="bulan ini" {{ $currentRange === 'bulan ini' ? 'selected' : '' }}>Bulan Ini</option>
            <option value="tahun ini" {{ $currentRange === 'tahun ini' ? 'selected' : '' }}>Tahun Ini</option>
        </select>
    </form>
</div>


<!-- Error Messages -->
@if(session('error'))
<div class="p-4 border border-red-300 rounded-lg bg-red-50">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <i class="text-red-400 fas fa-exclamation-circle"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">
                Terjadi Kesalahan
            </h3>
            <div class="mt-2 text-sm text-red-700">
                <p>{{ session('error') }}</p>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Success Messages -->
@if(session('success'))
<div class="p-4 border border-green-300 rounded-lg bg-green-50">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <i class="text-green-400 fas fa-check-circle"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-green-800">
                Sukses
            </h3>
            <div class="mt-2 text-sm text-green-700">
                <p>{{ session('success') }}</p>
            </div>
        </div>
    </div>
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
    <!-- Total Users -->
    <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Users Aktif</p>

                <p class="text-3xl font-bold text-gray-900">{{ number_format($totalActiveUsers) }}</p>
                <div class="flex gap-2 mt-1 text-xs text-gray-500">
                    <span>{{ $totalStudents }} Member</span>
                    <span>|</span>
                    <span>{{ $totalTeachers }} Monitor</span>
                    <span>|</span>
                    <span>{{ $totalParents }} Family</span>
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
                <p class="text-3xl font-bold text-gray-900">{{ $activeChallenges }}</p>
                <p class="text-xs text-gray-500 mt-1">Challenge dengan end date ≥ hari ini pada rentang terpilih</p>
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
                <p class="text-sm font-medium text-gray-600">Habit Aktif</p>
                <p class="text-3xl font-bold text-gray-900">{{ $activeHabits }}</p>
                <div class="flex gap-2 mt-1 text-xs">
                    <span class="text-green-600">{{ $doneHabits }} Done</span>
                    <span>ƒ?›</span>
                    <span class="text-red-600">{{ $notDoneHabits }} Missed</span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg">
                <i class="text-xl text-purple-600 fa fa-sync-alt"></i>
            </div>
        </div>
    </div>

    <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Jumlah Refleksi</p>
                <p class="text-3xl font-bold text-gray-900">{{ $reflectionsToday }}</p>
                <p class="text-xs text-gray-500 mt-1">Terekam pada rentang terpilih</p>
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
            <h3 class="mb-1 font-semibold">Kelola Users</h3>
            <p class="text-sm text-blue-100">Data member, monitor, family</p>
        </div>
    </a>

    <a href="{{ route('admin.challenges.index') }}"
        class="p-4 text-white transition-all rounded-lg bg-[#22C55E] hover:bg-[#16A34A] hover:shadow-md">
        <div class="flex flex-col items-center text-center">
            <div class="flex items-center justify-center w-12 h-12 mb-3  rounded-full bg-opacity-20">
                <i class="text-xl fa fa-flag-checkered"></i>
            </div>
            <h3 class="mb-1 font-semibold">Kelola Challenge</h3>
            <p class="text-sm text-green-100">Tantangan untuk member</p>
        </div>
    </a>

    <a href="{{ route('admin.habits.index') }}"
        class="p-4 text-white transition-all rounded-lg bg-[#8B5CF6] hover:bg-[#7C3AED] hover:shadow-md">
        <div class="flex flex-col items-center text-center">
            <div class="flex items-center justify-center w-12 h-12 mb-3  rounded-full bg-opacity-20">
                <i class="text-xl fa fa-tasks"></i>
            </div>
            <h3 class="mb-1 font-semibold">Kelola Habits</h3>
            <p class="text-sm text-purple-100">Kebiasaan harian/mingguan</p>
        </div>
    </a>

    <a href="{{ route('admin.rewards.index') }}"
        class="p-4 text-white transition-all rounded-lg bg-[#F59E0B] hover:bg-[#D97706] hover:shadow-md">
        <div class="flex flex-col items-center text-center">
            <div class="flex items-center justify-center w-12 h-12 mb-3  rounded-full bg-opacity-20">
                <i class="text-xl fa fa-gift"></i>
            </div>
            <h3 class="mb-1 font-semibold">Kelola Reward</h3>
            <p class="text-sm text-amber-100">Reward yang bisa diredeem oleh member</p>
        </div>
    </a>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <!-- Leaderboard & Top Performers -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-soft">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Top 10 Leaderboard</h3>
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
                        <img src="{{ Storage::url($student['avatar_url']) }}" alt="{{ $student['name'] }}"
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
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($recentActivities as $activity)
                <div class="flex items-start space-x-3">
                    <div class="flex items-center justify-center w-8 h-8 mt-1 rounded-full
                        @if($activity['type'] === 'challenge_completion') bg-green-100 text-green-600
                        @elseif($activity['type'] === 'habit_completion') bg-emerald-100 text-emerald-600
                        @elseif($activity['type'] === 'new_challenge') bg-blue-100 text-blue-600
                        @elseif($activity['type'] === 'appreciation') bg-purple-100 text-purple-600
                        @else bg-gray-100 text-gray-600 @endif">
                        <i class="text-sm fa
                            @if($activity['type'] === 'challenge_completion') fa-flag-checkered
                            @elseif($activity['type'] === 'habit_completion') fa-check-circle
                            @elseif($activity['type'] === 'new_challenge') fa-plus-circle
                            @elseif($activity['type'] === 'appreciation') fa-heart
                            @else fa-info-circle @endif"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-800">{{ $activity['message'] }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $activity['timestamp'] ? \Carbon\Carbon::parse($activity['timestamp'])->diffForHumans() :
                            '-' }}
                        </p>

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
@php
    $moodTotal = array_sum($moodDistribution ?? []);
    $hasHabitTrends = !empty($habitTrends);
    $hasChallengeProgress = !empty($challengeProgress);
@endphp
<div class="grid grid-cols-1 gap-6 mt-6 lg:grid-cols-3">
    <!-- Mood Distribution -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-soft">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Distribusi Mood Member</h3>
        </div>
        <div class="p-6">
            @if($moodTotal > 0)
            <canvas id="moodChart" width="400" height="250"></canvas>
            @else
            <div class="py-8 text-center text-gray-500">
                <i class="mb-2 text-4xl fa fa-smile"></i>
                <p>Data mood belum tersedia</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Habit Trends -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-soft">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Tren Habit Completion</h3>
        </div>
        <div class="p-6">
            @if(!$hasHabitTrends)
            <div class="text-center text-gray-500 py-8">
                <i class="mb-2 text-4xl fa fa-chart-line"></i>
                <p>Data tren habit belum tersedia</p>
            </div>
            @else
            <canvas id="habitTrendChart" width="400" height="250"></canvas>
            @endif
        </div>
    </div>

    <!-- Challenge Progress -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-soft">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Tren Challenge Completion</h3>
        </div>
        <div class="p-6">
            @if(!$hasChallengeProgress)
            <div class="text-center text-gray-500 py-8">
                <i class="mb-2 text-4xl fa fa-flag-checkered"></i>
                <p>Data tren challenge belum tersedia</p>
            </div>
            @else
            <canvas id="challengeTrendChart" width="400" height="250"></canvas>
            @endif
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
    @if($moodTotal > 0)
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
    @endif

     // Habit Trends Chart - HANYA di-render jika ada data
    @if($hasHabitTrends)
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
                    fill: true,
                    borderWidth: 2
                },
                {
                    label: 'Habit Not Done',
                    data: @json(array_column($habitTrends, 'not_done')),
                    borderColor: '#EF4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.3,
                    fill: true,
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Habit'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Minggu'
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
    @endif

    // Challenge Trends Chart - HANYA di-render jika ada data
    @if($hasChallengeProgress)
    const challengeCtx = document.getElementById('challengeTrendChart').getContext('2d');
    const challengeChart = new Chart(challengeCtx, {
        type: 'bar',
        data: {
            labels: @json(array_column($challengeProgress, 'week')),
            datasets: [
                {
                    label: 'Challenge Done',
                    data: @json(array_column($challengeProgress, 'done')),
                    backgroundColor: 'rgba(34, 197, 94, 0.7)',
                    borderColor: '#22C55E',
                    borderWidth: 1
                },
                {
                    label: 'Challenge Not Done',
                    data: @json(array_column($challengeProgress, 'not_done')),
                    backgroundColor: 'rgba(239, 68, 68, 0.7)',
                    borderColor: '#EF4444',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Challenge'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Periode'
                    }
                }
            }
        }
    });
    @endif
</script>
@endpush

@endsection
