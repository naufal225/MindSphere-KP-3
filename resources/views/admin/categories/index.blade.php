@extends('components.admin.layout.app')

@section('header', 'Manajemen Kategori')
@section('subtitle', 'Kelola data kategori konten')

@section('content')
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Daftar Kategori</h1>
            <p class="text-gray-600">Total: {{ $stats['total'] }} kategori</p>
        </div>
        <a href="{{ route('admin.categories.create') }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
            <i class="mr-2 fa-solid fa-plus"></i> Tambah Kategori
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
    <form method="GET" action="{{ route('admin.categories.index') }}">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
            <!-- Search Input -->
            <div>
                <label for="search" class="block mb-2 text-sm font-medium text-gray-700">
                    <i class="mr-1 fa-solid fa-search"></i> Cari Kategori
                </label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="Nama atau deskripsi kategori..."
                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Sort Field -->
            <div>
                <label for="sort_field" class="block mb-2 text-sm font-medium text-gray-700">
                    <i class="mr-1 fa-solid fa-sort"></i> Urutkan Berdasarkan
                </label>
                <select name="sort_field" id="sort_field"
                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="name" {{ request('sort_field')=='name' ? 'selected' : '' }}>Nama</option>
                    <option value="code" {{ request('sort_field')=='code' ? 'selected' : '' }}>Kode</option>
                    <option value="created_at" {{ request('sort_field')=='created_at' ? 'selected' : '' }}>Tanggal
                        Dibuat</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end space-x-3">
                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="mr-2 fa-solid fa-filter"></i> Terapkan Filter
                </button>
                <a href="{{ route('admin.categories.index') }}"
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
                <i class="text-xl text-blue-600 fa-solid fa-tags"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Kategori</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            </div>
        </div>
    </div>

    <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                <i class="text-xl text-green-600 fa-solid fa-repeat"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Habits</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_habits'] }}</p>
            </div>
        </div>
    </div>

    <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg">
                <i class="text-xl text-purple-600 fa-solid fa-flag"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Challenges</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_challenges'] }}</p>
            </div>
        </div>
    </div>

    <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-orange-100 rounded-lg">
                <i class="text-xl text-orange-600 fa-solid fa-medal"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Badges</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_badges'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Categories Table -->
<div class="overflow-hidden bg-white rounded-lg shadow-sm border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Daftar Kategori Konten</h3>
                <p class="text-sm text-gray-600">Menampilkan {{ $categories->count() }} dari {{ $categories->total() }}
                    kategori</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Sortir:</span>
                <select name="sort_direction" onchange="this.form.submit()"
                    class="text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    <option value="asc" {{ request('sort_direction', 'asc' )=='asc' ? 'selected' : '' }}>A-Z</option>
                    <option value="desc" {{ request('sort_direction')=='desc' ? 'selected' : '' }}>Z-A</option>
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
                        <i class="mr-1 fa-solid fa-tag"></i> Kode
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-heading"></i> Nama Kategori
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-align-left"></i> Deskripsi
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-chart-bar"></i> Statistik
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
                @forelse($categories as $category)
                <tr class="transition-colors hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                        $codeColors = [
                        'SA' => 'bg-blue-100 text-blue-800',
                        'SI' => 'bg-green-100 text-green-800',
                        'GM' => 'bg-purple-100 text-purple-800',
                        'KL' => 'bg-orange-100 text-orange-800',
                        'KR' => 'bg-red-100 text-red-800'
                        ];
                        @endphp
                        <span
                            class="inline-flex items-center px-3 py-1 text-sm font-bold rounded-full {{ $codeColors[$category->code] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $category->code }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-900">{{ $category->name }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-500 max-w-xs truncate">{{ $category->description }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col space-y-1 text-xs">
                            <div class="flex items-center space-x-2">
                                <span class="text-gray-500">Habits:</span>
                                <span class="font-semibold text-green-600">{{ $category->habits_count }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-gray-500">Challenges:</span>
                                <span class="font-semibold text-purple-600">{{ $category->challenges_count }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-gray-500">Badges:</span>
                                <span class="font-semibold text-orange-600">{{ $category->badges_count }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-gray-500">Reflections:</span>
                                <span class="font-semibold text-blue-600">{{ $category->reflections_count }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                        {{ $category->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="{{ route('admin.categories.show', $category->id) }}"
                                class="p-2 text-blue-600 transition-colors rounded-lg hover:bg-blue-50"
                                title="Lihat Detail">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.categories.edit', $category->id) }}"
                                class="p-2 text-yellow-600 transition-colors rounded-lg hover:bg-yellow-50"
                                title="Edit Kategori">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST"
                                class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-2 text-red-600 transition-colors rounded-lg hover:bg-red-50"
                                    title="Hapus Kategori"
                                    onclick="return confirm('Yakin ingin menghapus kategori {{ $category->name }}?')">
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
                            <i class="mb-3 text-4xl fa-solid fa-tags"></i>
                            <p class="text-lg font-medium text-gray-600">Tidak ada data kategori</p>
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
@if($categories->hasPages())
<div class="mt-6">
    {{ $categories->withQueryString()->links() }}
</div>
@endif

@endsection
