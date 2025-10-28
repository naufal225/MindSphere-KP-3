@extends('components.admin.layout.app')

@section('header', 'Detail User')
@section('subtitle', 'Informasi lengkap tentang pengguna')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center space-x-3">
            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                <i class="text-xl text-blue-600 fa-solid fa-user-circle"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Pengguna</h1>
                <p class="text-gray-600">Informasi lengkap tentang {{ $user->name }}</p>
            </div>
        </div>
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

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="overflow-hidden bg-white rounded-lg shadow-sm border border-gray-100">
                <!-- Profile Header -->
                <div class="p-6 text-center bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="relative inline-block">
                        @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                            class="w-32 h-32 mx-auto rounded-full shadow-lg border-4 border-white">
                        @else
                        <div
                            class="flex items-center justify-center w-32 h-32 mx-auto text-4xl font-bold text-white bg-gradient-to-r from-blue-500 to-purple-600 rounded-full shadow-lg border-4 border-white">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        @endif
                        <!-- Online Status Indicator -->
                        <div
                            class="absolute bottom-0 right-0 flex items-center justify-center w-8 h-8 bg-green-500 border-4 border-white rounded-full">
                            <i class="text-white fa-solid fa-check text-xs"></i>
                        </div>
                    </div>

                    <h3 class="mt-4 text-xl font-bold text-gray-800">{{ $user->name }}</h3>
                    <p class="text-gray-600">{{ $user->email }}</p>

                    <!-- Role Badge -->
                    @php
                    $roleColors = [
                    'admin' => 'bg-red-100 text-red-800 border-red-200',
                    'guru' => 'bg-purple-100 text-purple-800 border-purple-200',
                    'siswa' => 'bg-green-100 text-green-800 border-green-200',
                    'ortu' => 'bg-orange-100 text-orange-800 border-orange-200'
                    ];
                    $roleIcons = [
                    'admin' => 'fa-user-shield',
                    'guru' => 'fa-chalkboard-user',
                    'siswa' => 'fa-graduation-cap',
                    'ortu' => 'fa-user-group'
                    ];
                    @endphp
                    <span
                        class="inline-flex items-center px-4 py-2 mt-3 text-sm font-medium border rounded-full {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                        <i class="mr-2 fa-solid {{ $roleIcons[$user->role] ?? 'fa-user' }}"></i>
                        {{ ucfirst($user->role) }}
                    </span>
                </div>

                <!-- Stats -->
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- XP Stats -->
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-2">
                                <i class="mr-2 text-yellow-500 fa-solid fa-star"></i>
                                <span class="text-2xl font-bold text-gray-800">{{ number_format($user->xp) }}</span>
                            </div>
                            <p class="text-sm text-gray-600">Experience Points</p>
                        </div>

                        <!-- Level Stats -->
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-2">
                                <i class="mr-2 text-green-500 fa-solid fa-level-up-alt"></i>
                                <span class="text-2xl font-bold text-gray-800">Level {{ $user->level }}</span>
                            </div>
                            <p class="text-sm text-gray-600">Current Level</p>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-4">
                            <div class="flex justify-between mb-1 text-xs text-gray-600">
                                <span>Progress ke Level {{ $user->level + 1 }}</span>
                                <span>{{ min(($user->xp % 1000) / 10, 100) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-green-400 to-blue-500 h-2 rounded-full"
                                    style="width: {{ min(($user->xp % 1000) / 10, 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Details -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <!-- Tabs Navigation -->
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button type="button" data-tab="profile"
                            class="tab-button flex-1 py-4 px-6 text-center border-b-2 border-blue-500 text-blue-600 font-medium">
                            <i class="mr-2 fa-solid fa-user"></i>Informasi Profil
                        </button>
                        <button type="button" data-tab="activity"
                            class="tab-button flex-1 py-4 px-6 text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium">
                            <i class="mr-2 fa-solid fa-chart-line"></i>Aktivitas
                        </button>
                        {{-- <button type="button" data-tab="settings"
                            class="tab-button flex-1 py-4 px-6 text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium">
                            <i class="mr-2 fa-solid fa-cog"></i>Pengaturan
                        </button> --}}
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Profile Tab -->
                    <div id="profile-tab" class="tab-content">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">
                            <i class="mr-2 fa-solid fa-id-card"></i>Informasi Detail
                        </h3>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <!-- Basic Information -->
                            <div class="space-y-4">
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i
                                        class="w-8 h-8 p-2 mr-3 text-blue-600 bg-blue-100 rounded-lg fa-solid fa-id-badge"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">User ID</p>
                                        <p class="font-medium text-gray-900">{{ $user->id }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i
                                        class="w-8 h-8 p-2 mr-3 text-green-600 bg-green-100 rounded-lg fa-solid fa-signature"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">Nama Lengkap</p>
                                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i
                                        class="w-8 h-8 p-2 mr-3 text-purple-600 bg-purple-100 rounded-lg fa-solid fa-envelope"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">Email</p>
                                        <p class="font-medium text-gray-900">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Information -->
                            <div class="space-y-4">
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i
                                        class="w-8 h-8 p-2 mr-3 text-red-600 bg-red-100 rounded-lg fa-solid fa-user-tag"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">Role</p>
                                        <p class="font-medium text-gray-900 capitalize">{{ $user->role }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i
                                        class="w-8 h-8 p-2 mr-3 text-orange-600 bg-orange-100 rounded-lg fa-solid fa-calendar-plus"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">Bergabung</p>
                                        <p class="font-medium text-gray-900">{{ $user->created_at ?
                                            $user->created_at->format('d M Y') : '-' }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i
                                        class="w-8 h-8 p-2 mr-3 text-indigo-600 bg-indigo-100 rounded-lg fa-solid fa-calendar-check"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">Terakhir Diupdate</p>
                                        <p class="font-medium text-gray-900">{{ $user->updated_at ?
                                            $user->updated_at->format('d M Y') : '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Avatar Information -->
                        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="mr-3 text-blue-600 fa-solid fa-image"></i>
                                <div>
                                    <h4 class="font-medium text-blue-900">Avatar Profile</h4>
                                    @if($user->avatar_url)
                                    <p class="text-sm text-blue-700">Avatar tersedia</p>
                                    <a href="{{ $user->avatar_url }}" target="_blank"
                                        class="inline-flex items-center mt-1 text-sm text-blue-600 hover:text-blue-800">
                                        <i class="mr-1 fa-solid fa-external-link"></i> Lihat gambar lengkap
                                    </a>
                                    @else
                                    <p class="text-sm text-blue-700">Menggunakan avatar default</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Tab -->
                    <div id="activity-tab" class="hidden tab-content">
                        <h3 class="mb-6 text-lg font-semibold text-gray-800">
                            <i class="mr-2 fa-solid fa-chart-line"></i>Statistik & Progress
                        </h3>

                        <!-- Statistics Cards -->
                        <div class="grid grid-cols-2 gap-4 mb-6 md:grid-cols-4">
                            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                                <div class="flex items-center">
                                    <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                                        <i class="text-blue-600 fa-solid fa-flag"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600">Total Challenges</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_challenges'] }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                                <div class="flex items-center">
                                    <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                                        <i class="text-green-600 fa-solid fa-check-circle"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600">Challenges Selesai</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ $stats['completed_challenges'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                                <div class="flex items-center">
                                    <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg">
                                        <i class="text-purple-600 fa-solid fa-tasks"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600">Total Habits</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_habits'] }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                                <div class="flex items-center">
                                    <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-lg">
                                        <i class="text-yellow-600 fa-solid fa-star"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600">XP Diperoleh</p>
                                        <p class="text-2xl font-bold text-gray-900">{{
                                            number_format($stats['total_xp_earned']) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Challenges Section -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-semibold text-gray-800">
                                    <i class="mr-2 fa-solid fa-flag"></i>Challenges
                                </h4>
                                <span class="px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded-full">
                                    {{ $challenge_progress->count() }} challenge
                                </span>
                            </div>

                            @if($challenge_progress->count() > 0)
                            <div class="overflow-hidden bg-white border border-gray-200 rounded-lg shadow-sm">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    Challenge</th>
                                                <th
                                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    Kategori</th>
                                                <th
                                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    Status</th>
                                                <th
                                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    XP</th>
                                                <th
                                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    Tanggal Bergabung</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($challenge_progress as $challenge)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{
                                                        $challenge['title'] }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span
                                                        class="inline-flex px-2 text-xs font-semibold leading-5 text-blue-800 bg-blue-100 rounded-full">
                                                        {{ $challenge['category'] }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @php
                                                    $statusColors = [
                                                    'joined' => 'bg-yellow-100 text-yellow-800',
                                                    'submitted' => 'bg-blue-100 text-blue-800',
                                                    'completed' => 'bg-green-100 text-green-800'
                                                    ];
                                                    $statusText = [
                                                    'joined' => 'Dalam Pengerjaan',
                                                    'submitted' => 'Menunggu Verifikasi',
                                                    'completed' => 'Selesai'
                                                    ];
                                                    @endphp
                                                    <span
                                                        class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full {{ $statusColors[$challenge['status']] ?? 'bg-gray-100 text-gray-800' }}">
                                                        {{ $statusText[$challenge['status']] ?? $challenge['status'] }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center text-sm text-gray-900">
                                                        <i class="mr-1 text-yellow-500 fa-solid fa-star"></i>
                                                        {{ $challenge['xp_reward'] }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                    {{ $challenge['joined_at']->format('d M Y') }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @else
                            <div class="p-8 text-center bg-white border border-gray-200 rounded-lg">
                                <i class="mb-4 text-4xl text-gray-400 fa-solid fa-flag"></i>
                                <p class="text-gray-500">Belum ada challenges yang dikerjakan</p>
                            </div>
                            @endif
                        </div>

                        <!-- Habits Section -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-semibold text-gray-800">
                                    <i class="mr-2 fa-solid fa-tasks"></i>Habits
                                </h4>
                                <span class="px-3 py-1 text-sm bg-purple-100 text-purple-800 rounded-full">
                                    {{ $habit_progress->count() }} habit
                                </span>
                            </div>

                            @if($habit_progress->count() > 0)
                            <div class="overflow-hidden bg-white border border-gray-200 rounded-lg shadow-sm">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    Habit</th>
                                                <th
                                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    Kategori</th>
                                                <th
                                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    Progress</th>
                                                <th
                                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    XP/Hari</th>
                                                <th
                                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    Aktivitas Terakhir</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($habit_progress as $habit)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $habit['title'] }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 capitalize">{{ $habit['period'] }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span
                                                        class="inline-flex px-2 text-xs font-semibold leading-5 text-purple-800 bg-purple-100 rounded-full">
                                                        {{ $habit['category'] }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="w-32 mr-3">
                                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                                <div class="bg-green-500 h-2 rounded-full"
                                                                    style="width: {{ $habit['completion_rate'] }}%">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <span class="text-sm font-medium text-gray-700">{{
                                                            $habit['completion_rate'] }}%</span>
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ $habit['completed_logs'] }}/{{ $habit['total_logs'] }} hari
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center text-sm text-gray-900">
                                                        <i class="mr-1 text-yellow-500 fa-solid fa-star"></i>
                                                        {{ $habit['xp_reward'] }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                    @if($habit['last_activity'])
                                                    {{ \Carbon\Carbon::parse($habit['last_activity'])->format('d M Y')
                                                    }}
                                                    @else
                                                    -
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @else
                            <div class="p-8 text-center bg-white border border-gray-200 rounded-lg">
                                <i class="mb-4 text-4xl text-gray-400 fa-solid fa-tasks"></i>
                                <p class="text-gray-500">Belum ada habits yang dikerjakan</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Settings Tab (Placeholder) -->
                    <div id="settings-tab" class="hidden tab-content">
                        <div class="text-center py-8 text-gray-500">
                            <i class="mb-4 text-4xl fa-solid fa-sliders-h"></i>
                            <p>Pengaturan khusus pengguna akan ditampilkan di sini</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col gap-3 mt-6 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-gray-700 transition-colors bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">
                    <i class="mr-2 fa-solid fa-arrow-left"></i> Kembali ke Daftar
                </a>
                <a href="{{ route('admin.users.edit', $user->id) }}"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white transition-colors bg-yellow-600 rounded-lg hover:bg-yellow-700">
                    <i class="mr-2 fa-solid fa-edit"></i> Edit Pengguna
                </a>
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white transition-colors bg-red-600 rounded-lg hover:bg-red-700"
                        onclick="return confirm('Yakin ingin menghapus user {{ $user->name }}? Tindakan ini tidak dapat dibatalkan.')">
                        <i class="mr-2 fa-solid fa-trash"></i> Hapus Pengguna
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    // Set first tab as active by default
    if (tabButtons.length > 0) {
        tabButtons[0].click();
    }

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');

            // Update active tab button
            tabButtons.forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            this.classList.add('border-blue-500', 'text-blue-600');
            this.classList.remove('border-transparent', 'text-gray-500');

            // Show active tab content
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(`${tabId}-tab`).classList.remove('hidden');
        });
    });
});
 
</script>



<style>
    .tab-button {
        transition: all 0.3s ease;
    }

    .tab-button:hover {
        background-color: #f8fafc;
    }
</style>
@endpush
