@extends('components.admin.layout.app')

@section('header', 'Manajemen Tantangan')
@section('subtitle', 'Kelola tantangan dan kompetisi')

@section('content')
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Daftar Tantangan</h1>
            <p class="text-gray-600">Total: {{ $stats['total'] }} tantangan</p>
        </div>
        <a href="{{ route('admin.challenges.create') }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
            <i class="mr-2 fa-solid fa-plus"></i> Buat Tantangan
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
    <form method="GET" action="{{ route('admin.challenges.index') }}">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-5">
            <!-- Search Input -->
            <div>
                <label for="search" class="block mb-2 text-sm font-medium text-gray-700">
                    <i class="mr-1 fa-solid fa-search"></i> Cari Tantangan
                </label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="Judul atau deskripsi tantangan..."
                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Category Filter -->
            <div>
                <label for="category_id" class="block mb-2 text-sm font-medium text-gray-700">
                    <i class="mr-1 fa-solid fa-tags"></i> Filter Kategori
                </label>
                <select name="category_id" id="category_id"
                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id')==$category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Type Filter -->
            <div>
                <label for="type" class="block mb-2 text-sm font-medium text-gray-700">
                    <i class="mr-1 fa-solid fa-users"></i> Tipe Tantangan
                </label>
                <select name="type" id="type"
                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Tipe</option>
                    <option value="self" {{ request('type') == 'self' ? 'selected' : '' }}>Mandiri</option>
                    <option value="assigned" {{ request('type') == 'assigned' ? 'selected' : '' }}>Ditugaskan</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block mb-2 text-sm font-medium text-gray-700">
                    <i class="mr-1 fa-solid fa-clock"></i> Status
                </label>
                <select name="status" id="status"
                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Akan Datang</option>
                    <option value="ended" {{ request('status') == 'ended' ? 'selected' : '' }}>Berakhir</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end space-x-3">
                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="mr-2 fa-solid fa-filter"></i> Terapkan Filter
                </button>
                <a href="{{ route('admin.challenges.index') }}"
                    class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">
                    <i class="mr-2 fa-solid fa-refresh"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-6">
    <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                <i class="text-xl text-blue-600 fa-solid fa-flag"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Tantangan</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            </div>
        </div>
    </div>

    <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                <i class="text-xl text-green-600 fa-solid fa-user"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Mandiri</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['self'] }}</p>
            </div>
        </div>
    </div>

    <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg">
                <i class="text-xl text-purple-600 fa-solid fa-user-check"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Ditugaskan</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['assigned'] }}</p>
            </div>
        </div>
    </div>

    <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-emerald-100 rounded-lg">
                <i class="text-xl text-emerald-600 fa-solid fa-play-circle"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Aktif</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</p>
            </div>
        </div>
    </div>

    <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-cyan-100 rounded-lg">
                <i class="text-xl text-cyan-600 fa-solid fa-clock"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Akan Datang</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['upcoming'] }}</p>
            </div>
        </div>
    </div>

    <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-lg">
                <i class="text-xl text-gray-600 fa-solid fa-check-circle"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Berakhir</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['ended'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Challenges Table -->
<div class="overflow-hidden bg-white rounded-lg shadow-sm border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Daftar Tantangan</h3>
                <p class="text-sm text-gray-600">Menampilkan {{ $challenges->count() }} dari {{ $challenges->total() }}
                    tantangan</p>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-heading"></i> Judul Tantangan
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-tags"></i> Kategori & Tipe
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-calendar"></i> Periode & Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-star"></i> Reward
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-users"></i> Partisipan
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-user"></i> Dibuat Oleh
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-gear"></i> Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($challenges as $challenge)
                <tr class="transition-colors hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-gray-900">{{ $challenge->title }}</div>
                        <div class="text-sm text-gray-500 max-w-xs truncate">{{ $challenge->description }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col space-y-1">
                            @if($challenge->category)
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full">
                                <i class="mr-1 fa-solid fa-tag"></i>
                                {{ $challenge->category->name }}
                            </span>
                            @endif
                            @php
                                $typeColors = [
                                    'self' => 'bg-green-100 text-green-800',
                                    'assigned' => 'bg-purple-100 text-purple-800'
                                ];
                                $typeIcons = [
                                    'self' => 'fa-user',
                                    'assigned' => 'fa-user-check'
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {{ $typeColors[$challenge->type->value] }}">
                                <i class="mr-1 fa-solid {{ $typeIcons[$challenge->type->value] }}"></i>
                                {{ $challenge->type->value == 'self' ? 'Mandiri' : 'Ditugaskan' }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            <i class="mr-1 fa-solid fa-play text-green-500"></i>
                            {{ $challenge->start_date->format('d M Y') }}
                        </div>
                        <div class="text-sm text-gray-500">
                            <i class="mr-1 fa-solid fa-flag-checkered text-red-500"></i>
                            {{ $challenge->end_date->format('d M Y') }}
                        </div>
                        @php
                        $now = now();
                        $statusColor = '';
                        $statusIcon = '';
                        if ($now->between($challenge->start_date, $challenge->end_date)) {
                            $statusColor = 'bg-emerald-100 text-emerald-800';
                            $statusIcon = 'fa-play-circle';
                            $statusText = 'Aktif';
                        } elseif ($now->lt($challenge->start_date)) {
                            $statusColor = 'bg-cyan-100 text-cyan-800';
                            $statusIcon = 'fa-clock';
                            $statusText = 'Akan Datang';
                        } else {
                            $statusColor = 'bg-gray-100 text-gray-800';
                            $statusIcon = 'fa-check-circle';
                            $statusText = 'Berakhir';
                        }
                        @endphp
                        <span class="inline-flex items-center px-2 py-1 mt-1 text-xs font-medium rounded-full {{ $statusColor }}">
                            <i class="mr-1 fa-solid {{ $statusIcon }}"></i> {{ $statusText }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col space-y-2">
                            <!-- XP Reward -->
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 bg-yellow-100 rounded-lg">
                                    <i class="text-sm text-yellow-600 fa-solid fa-star"></i>
                                </div>
                                <div class="ml-2">
                                    <div class="text-sm font-bold text-yellow-600">
                                        {{ number_format($challenge->xp_reward) }} XP
                                    </div>
                                    <div class="text-xs text-gray-500">Experience Points</div>
                                </div>
                            </div>

                            <!-- Coin Reward -->
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 bg-amber-100 rounded-lg">
                                    <i class="text-sm text-amber-600 fa-solid fa-coins"></i>
                                </div>
                                <div class="ml-2">
                                    <div class="text-sm font-bold text-amber-600">
                                        {{ number_format($challenge->coin_reward) }} Koin
                                    </div>
                                    <div class="text-xs text-gray-500">Digital Coins</div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $challenge->participants_count ?? 0 }}</div>
                            <div class="text-xs text-gray-500">Partisipan</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($challenge->createdBy)
                        <div class="flex items-center">
                            @if($challenge->createdBy->avatar_url)
                            <img class="w-6 h-6 rounded-full" src="{{ Storage::url($challenge->createdBy->avatar_url) }}" alt="{{ $challenge->createdBy->name }}">
                            @else
                            <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ substr($challenge->createdBy->name, 0, 1) }}
                            </div>
                            @endif
                            <span class="ml-2 text-sm text-gray-900">{{ $challenge->createdBy->name }}</span>
                        </div>
                        @else
                        <span class="text-sm text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="{{ route('admin.challenges.show', $challenge->id) }}"
                                class="p-2 text-blue-600 transition-colors rounded-lg hover:bg-blue-50"
                                title="Lihat Detail">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.challenges.edit', $challenge->id) }}"
                                class="p-2 text-yellow-600 transition-colors rounded-lg hover:bg-yellow-50"
                                title="Edit Tantangan">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('admin.challenges.destroy', $challenge->id) }}" method="POST"
                                class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-2 text-red-600 transition-colors rounded-lg hover:bg-red-50"
                                    title="Hapus Tantangan"
                                    onclick="return confirm('Yakin ingin menghapus tantangan {{ $challenge->title }}?')">
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
                            <i class="mb-3 text-4xl fa-solid fa-flag"></i>
                            <p class="text-lg font-medium text-gray-600">Tidak ada data tantangan</p>
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
@if($challenges->hasPages())
<div class="mt-6">
    {{ $challenges->withQueryString()->links() }}
</div>
@endif
@endsection
