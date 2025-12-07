@extends('components.admin.layout.app')

@section('title', 'User Progress Report - MindSphere')

@section('content')
<div class="space-y-6">
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
    <!-- Page Header -->
    <div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">User Progress Report</h1>
            <p class="text-gray-600">Monitor and analyze member progress and activities</p>
        </div>
        <div class="flex gap-3">
            <button id="export-btn" class="px-4 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700">
                <i class="mr-2 fas fa-file-excel"></i>Export Excel
            </button>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="p-6 bg-white rounded-xl shadow-soft">
        <div class="flex flex-col gap-6">
            <!-- Main Filters Row -->
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-4">
                <!-- Divisi Filter -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Divisi</label>
                    <select id="class-filter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Divisi</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ $filters['class_id']==$class->id ? 'selected' : '' }}>
                            {{ $class->name }} ({{ $class->students_count }} member)
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Dari Tanggal</label>
                    <input type="date" id="start-date" value="{{ $filters['start_date'] }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Sampai Tanggal</label>
                    <input type="date" id="end-date" value="{{ $filters['end_date'] }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Minimum Activity -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Minimum Aktivitas</label>
                    <input type="number" id="min-activity" value="{{ $filters['min_activity'] }}" placeholder="Semua"
                        min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Advanced Filters Row -->
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                <!-- Mood Filter -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Filter Mood</label>
                    <select id="mood-filter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all" {{ $filters['mood_range']=='all' ? 'selected' : '' }}>Semua Mood</option>
                        <option value="happy-only" {{ $filters['mood_range']=='happy-only' ? 'selected' : '' }}>Hanya
                            Senang</option>
                        <option value="neutral+" {{ $filters['mood_range']=='neutral+' ? 'selected' : '' }}>Netral ke
                            Atas</option>
                    </select>
                </div>

                <!-- Sort By -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Urutkan Berdasarkan</label>
                    <select id="sort-by"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="xp_total" {{ $filters['sort_by']=='xp_total' ? 'selected' : '' }}>XP Tertinggi
                        </option>
                        <option value="habits_completed" {{ $filters['sort_by']=='habits_completed' ? 'selected' : ''
                            }}>Habit Terbanyak</option>
                        <option value="habit_streak" {{ $filters['sort_by']=='habit_streak' ? 'selected' : '' }}>Streak
                            Tertinggi</option>
                        <option value="reflections_written" {{ $filters['sort_by']=='reflections_written' ? 'selected'
                            : '' }}>Refleksi Terbanyak</option>
                        <option value="name" {{ $filters['sort_by']=='name' ? 'selected' : '' }}>Nama A-Z</option>
                    </select>
                </div>

                <!-- Include Inactive -->
                <div class="flex items-end">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" id="include-inactive" {{ $filters['include_inactive'] ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Sertakan member tanpa aktivitas</span>
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-4 border-t border-gray-200">
                <button id="apply-filters" class="px-6 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    <i class="mr-2 fas fa-filter"></i>Terapkan Filter
                </button>
                <button id="reset-filters" class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                    <i class="mr-2 fas fa-redo"></i>Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Students -->
        <div class="p-6 bg-white rounded-xl shadow-soft">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="text-blue-600 fas fa-users"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Member</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $userProgressData->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Average XP -->
        <div class="p-6 bg-white rounded-xl shadow-soft">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="text-green-600 fas fa-bolt"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Rata-rata XP</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($userProgressData->avg('xp_total') ??
                        0, 0) }}</p>
                </div>
            </div>
        </div>

        <!-- Total Activities -->
        <div class="p-6 bg-white rounded-xl shadow-soft">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i class="text-purple-600 fas fa-chart-line"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Aktivitas</p>
                    <p class="text-2xl font-bold text-gray-900">{{
                        number_format($userProgressData->sum('total_activities') ?? 0) }}</p>
                </div>
            </div>
        </div>

        <!-- Average Mood -->
        <div class="p-6 bg-white rounded-xl shadow-soft">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <i class="text-yellow-600 fas fa-smile"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Rata-rata Mood</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $userProgressData->count() > 0 ?
                        $userProgressData->avg('average_mood_score') ?
                        round($userProgressData->avg('average_mood_score'), 1) . '/5.0' : 'N/A' : 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Top 10 Students Chart -->
        <div class="p-6 bg-white rounded-xl shadow-soft">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Top 10 Member (XP Tertinggi)</h3>
            <div class="h-80">
                <canvas id="topStudentsChart"></canvas>
            </div>
        </div>

        <!-- Class Comparison Chart -->
        <div class="p-6 bg-white rounded-xl shadow-soft">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Perbandingan Rata-rata Aktivitas per Divisi</h3>
            <div class="h-80">
                <canvas id="classComparisonChart"></canvas>
            </div>
        </div>

        <!-- Mood Distribution Chart -->
        <div class="p-6 bg-white rounded-xl shadow-soft lg:col-span-2">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Distribusi Mood Member</h3>
            <div class="h-80">
                <canvas id="moodDistributionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="p-6 bg-white rounded-xl shadow-soft">
        <div class="flex flex-col justify-between gap-4 mb-6 sm:flex-row sm:items-center">
            <h3 class="text-lg font-semibold text-gray-900">Data Progress Member</h3>
            <div class="text-sm text-gray-600">
                Menampilkan {{ $userProgressData->count() }} member
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Member
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Divisi
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Level
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">XP
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Habit
                            Done</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Streak</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Challenge Done</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Reflections</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Avg
                            Mood</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Active Days</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($userProgressData as $progress)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10">
                                    <img class="w-10 h-10 rounded-full"
                                        src="{{ Storage::url($progress['user']->avatar_url) ?: asset('img/default-avatar.png') }}"
                                        alt="{{ $progress['user']->name }}">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $progress['user']->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $progress['user']->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $progress['class_name'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Lv. {{ $progress['level'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">{{
                            number_format($progress['xp_total']) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $progress['habits_completed']
                            }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $progress['habit_streak'] }} hari
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{
                            $progress['challenges_completed'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{
                            $progress['reflections_written'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            @php
                            $moodColors = [
                            'Sangat Senang' => 'bg-green-100 text-green-800',
                            'Senang' => 'bg-blue-100 text-blue-800',
                            'Biasa' => 'bg-yellow-100 text-yellow-800',
                            'Sedih' => 'bg-orange-100 text-orange-800',
                            'Sangat Sedih' => 'bg-red-100 text-red-800'
                            ];
                            $moodColor = $moodColors[$progress['average_mood']] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $moodColor }}">
                                {{ $progress['average_mood'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $progress['activity_days'] }}
                            hari</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-4 text-sm text-center text-gray-500">
                            Tidak ada data yang ditemukan dengan filter yang dipilih.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Initialize Charts
    initializeCharts();

    // Filter functionality
    document.getElementById('apply-filters').addEventListener('click', applyFilters);
    document.getElementById('reset-filters').addEventListener('click', resetFilters);
    document.getElementById('export-btn').addEventListener('click', exportData);

    // Enter key support for filters
    document.getElementById('min-activity').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') applyFilters();
    });

    function applyFilters() {
        const filters = {
            class_id: document.getElementById('class-filter').value,
            start_date: document.getElementById('start-date').value,
            end_date: document.getElementById('end-date').value,
            min_activity: document.getElementById('min-activity').value,
            mood_range: document.getElementById('mood-filter').value,
            sort_by: document.getElementById('sort-by').value,
            include_inactive: document.getElementById('include-inactive').checked
        };

        const queryString = new URLSearchParams(filters).toString();
        window.location.href = '{{ route("admin.user-progress.index") }}?' + queryString;
    }

    function resetFilters() {
        window.location.href = '{{ route("admin.user-progress.index") }}';
    }

    function exportData() {
        const filters = {
            class_id: document.getElementById('class-filter').value,
            start_date: document.getElementById('start-date').value,
            end_date: document.getElementById('end-date').value,
            min_activity: document.getElementById('min-activity').value,
            mood_range: document.getElementById('mood-filter').value,
            sort_by: document.getElementById('sort-by').value,
            include_inactive: document.getElementById('include-inactive').checked
        };

        const queryString = new URLSearchParams(filters).toString();
        window.location.href = '{{ route("admin.user-progress.export") }}?' + queryString;
    }

    function initializeCharts() {
        // Top Students Chart
        const topStudentsCtx = document.getElementById('topStudentsChart').getContext('2d');
        new Chart(topStudentsCtx, {
            type: 'bar',
            data: {
                labels: @json($topStudentsChart->pluck('name')),
                datasets: [{
                    label: 'XP Total',
                    data: @json($topStudentsChart->pluck('xp')),
                    backgroundColor: '#3B82F6',
                    borderColor: '#2563EB',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'XP'
                        }
                    }
                }
            }
        });

        // Class Comparison Chart
        const classComparisonCtx = document.getElementById('classComparisonChart').getContext('2d');
        new Chart(classComparisonCtx, {
            type: 'bar',
            data: {
                labels: @json($classComparisonChart->pluck('class_name')),
                datasets: [{
                    label: 'Rata-rata Aktivitas',
                    data: @json($classComparisonChart->pluck('avg_activities')),
                    backgroundColor: '#10B981',
                    borderColor: '#059669',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Rata-rata Aktivitas'
                        }
                    }
                }
            }
        });

        // Mood Distribution Chart
        const moodDistributionCtx = document.getElementById('moodDistributionChart').getContext('2d');
        new Chart(moodDistributionCtx, {
            type: 'pie',
            data: {
                labels: @json($moodDistributionChart->pluck('mood')),
                datasets: [{
                    data: @json($moodDistributionChart->pluck('count')),
                    backgroundColor: [
                        '#10B981', // Sangat Senang - Green
                        '#3B82F6', // Senang - Blue
                        '#F59E0B', // Biasa - Yellow
                        '#F97316', // Sedih - Orange
                        '#EF4444'  // Sangat Sedih - Red
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
});
</script>
@endpush
