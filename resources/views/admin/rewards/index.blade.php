@extends('components.admin.layout.app')

@section('header', 'Manajemen Reward')
@section('subtitle', 'Kelola hadiah dan reward yang bisa ditukar dengan koin')

@section('content')

<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Daftar Reward</h1>
            <p class="text-gray-600">Total: {{ $rewards->total() }} reward</p>
        </div>
        <a href="{{ route('admin.rewards.create') }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
            <i class="mr-2 fa-solid fa-plus"></i> Tambah Reward
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

<!-- Stats Cards -->
<div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-4">
    <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                <i class="text-xl text-blue-600 fa-solid fa-gift"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Reward</p>
                <p class="text-2xl font-bold text-gray-900">{{ $statistics['total_rewards'] }}</p>
            </div>
        </div>
    </div>

    <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                <i class="text-xl text-green-600 fa-solid fa-check-circle"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Aktif</p>
                <p class="text-2xl font-bold text-gray-900">{{ $statistics['active_rewards'] }}</p>
            </div>
        </div>
    </div>

    <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-lg">
                <i class="text-xl text-red-600 fa-solid fa-exclamation-triangle"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Habis Stok</p>
                <p class="text-2xl font-bold text-gray-900">{{ $statistics['out_of_stock'] }}</p>
            </div>
        </div>
    </div>

    <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-lg">
                <i class="text-xl text-yellow-600 fa-solid fa-coins"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Nilai Koin</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($statistics['total_coin_value']) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter -->
<div class="p-6 mb-6 bg-white rounded-lg shadow-sm border border-gray-100">
    <form method="GET" action="{{ route('admin.rewards.index') }}">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
            <!-- Search Input -->
            <div>
                <label for="search" class="block mb-2 text-sm font-medium text-gray-700">
                    <i class="mr-1 fa-solid fa-search"></i> Cari Reward
                </label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="Nama, deskripsi..."
                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block mb-2 text-sm font-medium text-gray-700">
                    <i class="mr-1 fa-solid fa-toggle-on"></i> Status
                </label>
                <select name="status" id="status"
                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <!-- Type Filter -->
            <div>
                <label for="type" class="block mb-2 text-sm font-medium text-gray-700">
                    <i class="mr-1 fa-solid fa-tag"></i> Tipe Reward
                </label>
                <select name="type" id="type"
                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Tipe</option>
                    <option value="physical" {{ request('type') == 'physical' ? 'selected' : '' }}>Fisik</option>
                    <option value="digital" {{ request('type') == 'digital' ? 'selected' : '' }}>Digital</option>
                    <option value="voucher" {{ request('type') == 'voucher' ? 'selected' : '' }}>Voucher</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end space-x-3">
                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="mr-2 fa-solid fa-filter"></i> Terapkan Filter
                </button>
                <a href="{{ route('admin.rewards.index') }}"
                    class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">
                    <i class="mr-2 fa-solid fa-refresh"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Rewards Table -->
<div class="overflow-hidden bg-white rounded-lg shadow-sm border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Daftar Reward Sistem</h3>
                <p class="text-sm text-gray-600">Menampilkan {{ $rewards->count() }} dari {{ $rewards->total() }} reward
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Sortir:</span>
                <select name="sort_by" onchange="this.form.submit()" form="filter-form"
                    class="text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    <option value="created_at" {{ request('sort_by', 'created_at') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nama A-Z</option>
                    <option value="coin_cost" {{ request('sort_by') == 'coin_cost' ? 'selected' : '' }}>Harga Terendah</option>
                    <option value="stock" {{ request('sort_by') == 'stock' ? 'selected' : '' }}>Stok Terbanyak</option>
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
                        <i class="mr-1 fa-solid fa-image"></i> Gambar
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-gift"></i> Nama Reward
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-coins"></i> Biaya Koin
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-boxes"></i> Stok
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-tag"></i> Tipe
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-toggle-on"></i> Status
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-user"></i> Dibuat Oleh
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-calendar"></i> Dibuat
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-gear"></i> Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($rewards as $reward)
                @php
                    $typeColors = [
                        'physical' => 'bg-blue-100 text-blue-800 border-blue-200',
                        'digital' => 'bg-green-100 text-green-800 border-green-200',
                        'voucher' => 'bg-purple-100 text-purple-800 border-purple-200'
                    ];

                    $typeLabels = [
                        'physical' => 'Fisik',
                        'digital' => 'Digital',
                        'voucher' => 'Voucher'
                    ];
                @endphp
                <tr class="transition-colors hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($reward->image_url)
                        <img src="{{ $reward->image_url }}" alt="{{ $reward->name }}"
                            class="object-cover w-12 h-12 rounded-lg shadow-sm">
                        @else
                        <div
                            class="flex items-center justify-center w-12 h-12 text-lg font-bold text-white bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-sm">
                            <i class="fa-solid fa-gift"></i>
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-gray-900">{{ $reward->name }}</div>
                        @if($reward->description)
                        <div class="text-xs text-gray-500 truncate max-w-xs">{{ $reward->description }}</div>
                        @endif
                        <div class="flex items-center mt-1 text-xs text-gray-400">
                            <i class="mr-1 fa-solid fa-hashtag"></i>ID: {{ $reward->id }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-10 h-10 bg-yellow-100 rounded-full mr-2">
                                <i class="text-yellow-600 fa-solid fa-coins"></i>
                            </div>
                            <div>
                                <div class="text-lg font-bold text-gray-900">{{ number_format($reward->coin_cost) }}</div>
                                <div class="text-xs text-gray-500">Koin</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($reward->stock == -1)
                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                            <i class="mr-1 fa-solid fa-infinity"></i> Unlimited
                        </span>
                        @elseif($reward->stock == 0)
                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                            <i class="mr-1 fa-solid fa-exclamation-circle"></i> Habis
                        </span>
                        @elseif($reward->stock < 10)
                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                            <i class="mr-1 fa-solid fa-exclamation-triangle"></i> {{ $reward->stock }} tersisa
                        </span>
                        @else
                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                            <i class="mr-1 fa-solid fa-check-circle"></i> {{ $reward->stock }} tersisa
                        </span>
                        @endif
                        @if($reward->validity_days)
                        <div class="mt-1 text-xs text-gray-500">
                            <i class="mr-1 fa-solid fa-clock"></i> Berlaku {{ $reward->validity_days }} hari
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full border {{ $typeColors[$reward->type] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                            <i class="mr-1 fa-solid fa-{{ $reward->type == 'physical' ? 'box' : ($reward->type == 'digital' ? 'mobile-screen' : 'ticket') }}"></i>
                            {{ $typeLabels[$reward->type] ?? $reward->type }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($reward->is_active)
                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                            <i class="mr-1 fa-solid fa-check-circle"></i> Aktif
                        </span>
                        @else
                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                            <i class="mr-1 fa-solid fa-ban"></i> Nonaktif
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $reward->creator->name ?? 'System' }}</div>
                        <div class="text-xs text-gray-500">{{ $reward->creator->email ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                        <div class="flex flex-col">
                            <span>{{ $reward->created_at->format('d M Y') }}</span>
                            <span class="text-xs text-gray-400">{{ $reward->created_at->format('H:i') }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                        <div class="flex items-center justify-center space-x-1">
                            <a href="{{ route('admin.rewards.edit', $reward->id) }}"
                                class="p-2 text-yellow-600 transition-colors rounded-lg hover:bg-yellow-50 group"
                                title="Edit Reward" data-tooltip="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>

                            <form action="{{ route('admin.rewards.toggle-status', $reward->id) }}" method="POST" class="inline">
                                @csrf
                                @method('POST')
                                <button type="submit"
                                    class="p-2 {{ $reward->is_active ? 'text-orange-600 hover:bg-orange-50' : 'text-green-600 hover:bg-green-50' }} transition-colors rounded-lg group"
                                    title="{{ $reward->is_active ? 'Nonaktifkan' : 'Aktifkan' }}" data-tooltip="{{ $reward->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                    onclick="return confirm('Yakin ingin {{ $reward->is_active ? 'nonaktifkan' : 'aktifkan' }} reward {{ addslashes($reward->name) }}?')">
                                    <i class="fa-solid fa-{{ $reward->is_active ? 'ban' : 'check' }}"></i>
                                </button>
                            </form>

                            <form action="{{ route('admin.rewards.destroy', $reward->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-2 text-red-600 transition-colors rounded-lg hover:bg-red-50 group"
                                    title="Hapus Reward" data-tooltip="Hapus"
                                    onclick="return confirm('Yakin ingin menghapus reward {{ addslashes($reward->name) }}? Tindakan ini tidak dapat dibatalkan!')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <i class="mb-4 text-5xl fa-solid fa-gift"></i>
                            <p class="mb-2 text-lg font-medium text-gray-600">Belum ada reward</p>
                            <p class="text-sm text-gray-500">Tambah reward pertama untuk memulai program hadiah</p>
                            <a href="{{ route('admin.rewards.create') }}"
                                class="inline-flex items-center px-4 py-2 mt-4 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                                <i class="mr-2 fa-solid fa-plus"></i> Tambah Reward Pertama
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Table Footer -->
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-600">
                Menampilkan {{ $rewards->firstItem() ?? 0 }} - {{ $rewards->lastItem() ?? 0 }} dari {{ $rewards->total() }}
                hasil
            </div>
            @if($rewards->hasPages())
            <div class="flex space-x-2">
                {{ $rewards->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Hidden form for sorting -->
<form id="filter-form" method="GET" action="{{ route('admin.rewards.index') }}" class="hidden">
    @if(request('search'))
        <input type="hidden" name="search" value="{{ request('search') }}">
    @endif
    @if(request('status'))
        <input type="hidden" name="status" value="{{ request('status') }}">
    @endif
    @if(request('type'))
        <input type="hidden" name="type" value="{{ request('type') }}">
    @endif
    @if(request('sort_by'))
        <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
    @endif
</form>

<style>
    .group:hover [data-tooltip]::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: -30px;
        left: 50%;
        transform: translateX(-50%);
        background: #374151;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 10;
    }

    .group {
        position: relative;
    }
</style>

@endsection
