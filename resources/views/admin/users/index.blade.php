@extends('components.admin.layout.app')

@section('header', 'Manajemen Pengguna')
@section('subtitle', 'Kelola data pengguna sistem')

@section('content')

@php
    use App\Http\Services\LevelService;
@endphp

<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Daftar Pengguna</h1>
            <p class="text-gray-600">Total: {{ $users->total() }} pengguna</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
            <i class="mr-2 fa-solid fa-plus"></i> Tambah User
        </a>
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

<!-- Search & Filter -->
<div class="p-6 mb-6 bg-white rounded-lg shadow-sm border border-gray-100">
    <form method="GET" action="{{ route('admin.users.index') }}">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
            <!-- Search Input -->
            <div>
                <label for="search" class="block mb-2 text-sm font-medium text-gray-700">
                    <i class="mr-1 fa-solid fa-search"></i> Cari Pengguna
                </label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="Nama, email, atau username..."
                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Role Filter -->
            <div>
                <label for="role" class="block mb-2 text-sm font-medium text-gray-700">
                    <i class="mr-1 fa-solid fa-user-tag"></i> Filter Role
                </label>
                <select name="role" id="role"
                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role')==='admin' ? 'selected' : '' }}>
                        <i class="fa-solid fa-user-shield"></i> Admin
                    </option>
                    <option value="guru" {{ request('role')==='guru' ? 'selected' : '' }}>
                        <i class="fa-solid fa-chalkboard-user"></i> Guru
                    </option>
                    <option value="siswa" {{ request('role')==='siswa' ? 'selected' : '' }}>
                        <i class="fa-solid fa-graduation-cap"></i> Siswa
                    </option>
                    <option value="ortu" {{ request('role')==='ortu' ? 'selected' : '' }}>
                        <i class="fa-solid fa-user-group"></i> Orang Tua
                    </option>
                </select>
            </div>

            <!-- Class Filter -->
            <div>
                <label for="class_id" class="block mb-2 text-sm font-medium text-gray-700">
                    <i class="mr-1 fa-solid fa-chalkboard"></i> Filter Kelas
                </label>
                <select name="class_id" id="class_id"
                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ request('class_id')==$class->id ? 'selected' : '' }}>
                        {{ $class->name }} - {{ $class->teacher->name ?? 'Tidak ada guru' }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            {{-- <div>
                <label for="status" class="block mb-2 text-sm font-medium text-gray-700">
                    <i class="mr-1 fa-solid fa-circle-check"></i> Status
                </label>
                <select name="status" id="status"
                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status')==='active' ? 'selected' : '' }}>
                        <i class="fa-solid fa-circle-check text-green-500"></i> Aktif
                    </option>
                    <option value="inactive" {{ request('status')==='inactive' ? 'selected' : '' }}>
                        <i class="fa-solid fa-circle-pause text-gray-500"></i> Non-Aktif
                    </option>
                </select>
            </div> --}}

            <!-- Action Buttons -->
            <div class="flex items-end space-x-3">
                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="mr-2 fa-solid fa-filter"></i> Terapkan Filter
                </button>
                <a href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">
                    <i class="mr-2 fa-solid fa-refresh"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-4">
    <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                <i class="text-xl text-blue-600 fa-solid fa-users"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Pengguna</p>
                <p class="text-2xl font-bold text-gray-900">{{ $users->total() }}</p>
            </div>
        </div>
    </div>

    <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                <i class="text-xl text-green-600 fa-solid fa-graduation-cap"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Siswa</p>
                <p class="text-2xl font-bold text-gray-900">{{ $users->where('role', 'siswa')->count() }}</p>
            </div>
        </div>
    </div>

    <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg">
                <i class="text-xl text-purple-600 fa-solid fa-chalkboard-user"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Guru</p>
                <p class="text-2xl font-bold text-gray-900">{{ $users->where('role', 'guru')->count() }}</p>
            </div>
        </div>
    </div>

    <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-orange-100 rounded-lg">
                <i class="text-xl text-orange-600 fa-solid fa-user-group"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Orang Tua</p>
                <p class="text-2xl font-bold text-gray-900">{{ $users->where('role', 'ortu')->count() }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="overflow-hidden bg-white rounded-lg shadow-sm border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Daftar Pengguna Sistem</h3>
                <p class="text-sm text-gray-600">Menampilkan {{ $users->count() }} dari {{ $users->total() }} pengguna
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Sortir:</span>
                <select class="text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    <option>Terbaru</option>
                    <option>Nama A-Z</option>
                    <option>XP Tertinggi</option>
                </select>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-image"></i> Avatar
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-user"></i> Informasi Pengguna
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-at"></i> Username
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-shield"></i> Role
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-chart-line"></i> XP & Level
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-calendar"></i> Bergabung
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-gear"></i> Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                @php

                // Hitung progress level menggunakan LevelService
                $currentLevel = $user->level;
                $currentXp = $user->xp;

                // XP yang dibutuhkan untuk mencapai level saat ini
                $xpForCurrentLevel = LevelService::getXpForNextLevel($currentLevel - 1);

                // XP yang dibutuhkan untuk mencapai level berikutnya
                $xpForNextLevel = LevelService::getXpForNextLevel($currentLevel);

                // XP yang sudah diperoleh di level saat ini
                $xpInCurrentLevel = $currentXp - $xpForCurrentLevel;

                // XP yang dibutuhkan untuk naik ke level berikutnya
                $xpNeededForNextLevel = $xpForNextLevel - $xpForCurrentLevel;

                // Persentase progress
                $progressPercentage = $xpNeededForNextLevel > 0
                ? min(round(($xpInCurrentLevel / $xpNeededForNextLevel) * 100, 1), 100)
                : 100;
                @endphp
                <tr class="transition-colors hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->avatar_url)
                        <img src="{{ Storage::url($user->avatar_url) }}" alt="{{ $user->name }}"
                            class="w-10 h-10 rounded-full shadow-sm">
                        @else
                        <div
                            class="flex items-center justify-center w-10 h-10 text-lg font-bold text-white bg-gradient-to-r from-blue-500 to-blue-600 rounded-full shadow-sm">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-900">{{ $user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $user->username ? '@'.$user->username : '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                        $roleColors = [
                        'admin' => 'bg-red-100 text-red-800',
                        'guru' => 'bg-purple-100 text-purple-800',
                        'siswa' => 'bg-green-100 text-green-800',
                        'ortu' => 'bg-orange-100 text-orange-800'
                        ];
                        $roleIcons = [
                        'admin' => 'fa-user-shield',
                        'guru' => 'fa-chalkboard-user',
                        'siswa' => 'fa-graduation-cap',
                        'ortu' => 'fa-user-group'
                        ];
                        @endphp
                        <span
                            class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800' }}">
                            <i class="mr-1 fa-solid {{ $roleIcons[$user->role] ?? 'fa-user' }}"></i>
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center space-x-2">
                                <div class="flex items-center text-sm font-bold text-gray-900">
                                    <i class="mr-1 text-yellow-500 fa-solid fa-star"></i>
                                    {{ number_format($user->xp) }} XP
                                </div>
                                <span class="px-2 py-1 text-xs font-bold text-blue-800 bg-blue-100 rounded-full">
                                    Level {{ $user->level }}
                                </span>
                            </div>
                            <span class="text-xs text-gray-500">{{ $progressPercentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-gradient-to-r from-green-400 to-blue-500 h-1.5 rounded-full transition-all duration-300 ease-out"
                                style="width: {{ $progressPercentage }}%"></div>
                        </div>
                        <div class="flex justify-between mt-1 text-xs text-gray-500">
                            <span>{{ number_format($xpInCurrentLevel) }} XP</span>
                            <span>{{ number_format($xpNeededForNextLevel - $xpInCurrentLevel) }} XP tersisa</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                        {{ $user->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="{{ route('admin.users.show', $user->id) }}"
                                class="p-2 text-blue-600 transition-colors rounded-lg hover:bg-blue-50"
                                title="Lihat Detail">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                class="p-2 text-yellow-600 transition-colors rounded-lg hover:bg-yellow-50"
                                title="Edit Pengguna">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-2 text-red-600 transition-colors rounded-lg hover:bg-red-50"
                                    title="Hapus Pengguna"
                                    onclick="return confirm('Yakin ingin menghapus user {{ $user->name }}?')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <i class="mb-3 text-4xl fa-solid fa-users-slash"></i>
                            <p class="text-lg font-medium text-gray-600">Tidak ada data pengguna</p>
                            <p class="text-sm text-gray-500">Coba ubah filter pencarian Anda</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if($users->hasPages())
<div class="mt-6">
    {{ $users->links() }}
</div>
@endif

@endsection
