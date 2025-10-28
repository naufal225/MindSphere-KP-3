@extends('components.admin.layout.app')

@section('header', 'Manajemen Kebiasaan')
@section('subtitle', 'Kelola kebiasaan dan aktivitas rutin')

@section('content')
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Daftar Kebiasaan</h1>
            <p class="text-gray-600">Total: {{ $stats['total'] }} kebiasaan</p>
        </div>
        <a href="{{ route('admin.habits.create') }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
            <i class="mr-2 fa-solid fa-plus"></i> Buat Kebiasaan
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
<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
    <ul class="list-disc list-inside space-y-1">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Search & Filter -->
<div class="p-6 mb-6 bg-white rounded-lg shadow-sm border border-gray-100">
    <form method="GET" action="{{ route('admin.habits.index') }}">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
            <!-- Search Input -->
            <div>
                <label for="search" class="block mb-2 text-sm font-medium text-gray-700">
                    <i class="mr-1 fa-solid fa-search"></i> Cari Kebiasaan
                </label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="Judul atau deskripsi kebiasaan..."
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
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>



            <!-- Period Filter -->
            <div>
                <label for="period" class="block mb-2 text-sm font-medium text-gray-700">
                    <i class="mr-1 fa-solid fa-calendar"></i> Periode
                </label>
                <select name="period" id="period"
                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Periode</option>
                    <option value="daily" {{ request('period') == 'daily' ? 'selected' : '' }}>Harian</option>
                    <option value="weekly" {{ request('period') == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end space-x-3">
                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="mr-2 fa-solid fa-filter"></i> Terapkan Filter
                </button>
                <a href="{{ route('admin.habits.index') }}"
                    class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">
                    <i class="mr-2 fa-solid fa-refresh"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-5">
    <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                <i class="text-xl text-blue-600 fa-solid fa-repeat"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Kebiasaan</p>
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
            <div class="flex items-center justify-center w-12 h-12 bg-orange-100 rounded-lg">
                <i class="text-xl text-orange-600 fa-solid fa-calendar-day"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Harian</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['daily'] }}</p>
            </div>
        </div>
    </div>

    <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-indigo-100 rounded-lg">
                <i class="text-xl text-indigo-600 fa-solid fa-calendar-week"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Mingguan</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['weekly'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Habits Table -->
<div class="overflow-hidden bg-white rounded-lg shadow-sm border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Daftar Kebiasaan</h3>
                <p class="text-sm text-gray-600">Menampilkan {{ $habits->count() }} dari {{ $habits->total() }} kebiasaan</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Urutkan:</span>
                <select name="sort_field" onchange="this.form.submit()"
                    class="text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    <option value="created_at" {{ request('sort_field', 'created_at') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                    <option value="title" {{ request('sort_field') == 'title' ? 'selected' : '' }}>Judul A-Z</option>
                    <option value="period" {{ request('sort_field') == 'period' ? 'selected' : '' }}>Periode</option>
                </select>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-heading"></i> Judul Kebiasaan
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-tags"></i> Kategori 
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-calendar"></i> Periode
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-user"></i> Ditugaskan Oleh
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-chart-line"></i> Aktivitas
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-gear"></i> Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($habits as $habit)
                <tr class="transition-colors hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-gray-900">{{ $habit->title }}</div>
                        <div class="text-sm text-gray-500 max-w-xs truncate">{{ $habit->description }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col space-y-1">
                            @if($habit->category)
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full">
                                <i class="mr-1 fa-solid fa-tag"></i>
                                {{ $habit->category->name }}
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
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {{ $typeColors[$habit->type->value] }}">
                                <i class="mr-1 fa-solid {{ $typeIcons[$habit->type->value] }}"></i>
                                {{ $habit->type->value == 'self' ? 'Mandiri' : 'Ditugaskan' }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $periodColors = [
                                'daily' => 'bg-orange-100 text-orange-800',
                                'weekly' => 'bg-indigo-100 text-indigo-800'
                            ];
                            $periodIcons = [
                                'daily' => 'fa-calendar-day',
                                'weekly' => 'fa-calendar-week'
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {{ $periodColors[$habit->period->value] }}">
                            <i class="mr-1 fa-solid {{ $periodIcons[$habit->period->value] }}"></i>
                            {{ $habit->period->value == 'daily' ? 'Harian' : 'Mingguan' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($habit->assignedBy)
                        <div class="flex items-center">
                            @if($habit->assignedBy->avatar_url)
                            <img class="w-6 h-6 rounded-full" src="{{ $habit->assignedBy->avatar_url }}" alt="{{ $habit->assignedBy->name }}">
                            @else
                            <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ substr($habit->assignedBy->name, 0, 1) }}
                            </div>
                            @endif
                            <span class="ml-2 text-sm text-gray-900">{{ $habit->assignedBy->name }}</span>
                        </div>
                        @else
                        <span class="text-sm text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $habit->logs_count ?? 0 }}</div>
                        <div class="text-xs text-gray-500">Total Log</div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="{{ route('admin.habits.show', $habit->id) }}"
                                class="p-2 text-blue-600 transition-colors rounded-lg hover:bg-blue-50"
                                title="Lihat Detail">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.habits.edit', $habit->id) }}"
                                class="p-2 text-yellow-600 transition-colors rounded-lg hover:bg-yellow-50"
                                title="Edit Kebiasaan">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('admin.habits.destroy', $habit->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-2 text-red-600 transition-colors rounded-lg hover:bg-red-50"
                                    title="Hapus Kebiasaan"
                                    onclick="return confirm('Yakin ingin menghapus kebiasaan {{ $habit->title }}?')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <i class="mb-3 text-4xl fa-solid fa-repeat"></i>
                            <p class="text-lg font-medium text-gray-600">Tidak ada data kebiasaan</p>
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
@if($habits->hasPages())
<div class="mt-6">
    {{ $habits->withQueryString()->links() }}
</div>
@endif
@endsection
