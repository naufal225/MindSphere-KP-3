@extends('components.admin.layout.app')

@section('header', 'Manajemen Badge')
@section('subtitle', 'Kelola badge dan penghargaan pengguna')

@section('content')
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Daftar Badge</h1>
            <p class="text-gray-600">Total: {{ $badges->total() }} badge</p>
        </div>
        <a href="{{ route('admin.badges.create') }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
            <i class="mr-2 fa-solid fa-plus"></i> Buat Badge
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
    <form method="GET" action="{{ route('admin.badges.index') }}">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <!-- Search Input -->
            <div>
                <label for="search" class="block mb-2 text-sm font-medium text-gray-700">
                    <i class="mr-1 fa-solid fa-search"></i> Cari Badge
                </label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="Nama atau deskripsi badge..."
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

            <!-- Action Buttons -->
            <div class="flex items-end space-x-3">
                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="mr-2 fa-solid fa-filter"></i> Terapkan Filter
                </button>
                <a href="{{ route('admin.badges.index') }}"
                    class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">
                    <i class="mr-2 fa-solid fa-refresh"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-3">
    <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                <i class="text-xl text-blue-600 fa-solid fa-medal"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Badge</p>
                <p class="text-2xl font-bold text-gray-900">{{ $badges->total() }}</p>
            </div>
        </div>
    </div>

    <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                <i class="text-xl text-green-600 fa-solid fa-users"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Badge dengan Pengguna</p>
                <p class="text-2xl font-bold text-gray-900">{{ $badges->where('users_count', '>', 0)->count() }}</p>
            </div>
        </div>
    </div>

    <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg">
                <i class="text-xl text-purple-600 fa-solid fa-star"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Pengguna Terbadge</p>
                <p class="text-2xl font-bold text-gray-900">{{ $badges->sum('users_count') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Badges Table -->
<div class="overflow-hidden bg-white rounded-lg shadow-sm border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Daftar Badge</h3>
                <p class="text-sm text-gray-600">Menampilkan {{ $badges->count() }} dari {{ $badges->total() }} badge
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Urutkan:</span>
                <select name="sort_field" onchange="this.form.submit()"
                    class="text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    <option value="created_at" {{ request('sort_field', 'created_at' )=='created_at' ? 'selected' : ''
                        }}>Terbaru</option>
                    <option value="name" {{ request('sort_field')=='name' ? 'selected' : '' }}>Nama A-Z</option>
                    <option value="xp_required" {{ request('sort_field')=='xp_required' ? 'selected' : '' }}>XP Required
                    </option>
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
                        <i class="mr-1 fa-solid fa-medal"></i> Badge
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-tags"></i> Kategori
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-star"></i> XP Dibutuhkan
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-users"></i> Pengguna
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                        <i class="mr-1 fa-solid fa-gear"></i> Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($badges as $badge)
                <tr class="transition-colors hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-start space-x-3">
                            @if($badge->icon_url)
                            <img src="{{ asset('storage/' . $badge->icon_url) }}" alt="{{ $badge->name }}"
                                class="w-10 h-10 rounded-lg object-cover border border-gray-200">
                            @else
                            <div class="flex items-center justify-center w-10 h-10 bg-gray-200 rounded-lg">
                                <i class="text-gray-500 fa-solid fa-medal"></i>
                            </div>
                            @endif
                            <div>
                                <div class="text-sm font-semibold text-gray-900">{{ $badge->name }}</div>
                                <div class="text-sm text-gray-500 max-w-xs truncate">{{ Str::limit($badge->description,
                                    60) }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($badge->category)
                        <span
                            class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full">
                            <i class="mr-1 fa-solid fa-tag"></i>
                            {{ $badge->category->name }}
                        </span>
                        @else
                        <span class="text-sm text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($badge->xp_required)
                        <span
                            class="inline-flex items-center px-2 py-1 text-xs font-medium text-orange-700 bg-orange-100 rounded-full">
                            <i class="mr-1 fa-solid fa-star"></i>
                            {{ number_format($badge->xp_required) }} XP
                        </span>
                        @else
                        <span class="text-sm text-gray-500">Tidak ada syarat XP</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $badge->users_count ?? 0 }}</div>
                        <div class="text-xs text-gray-500">Pengguna</div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="{{ route('admin.badges.show', $badge->id) }}"
                                class="p-2 text-blue-600 transition-colors rounded-lg hover:bg-blue-50"
                                title="Lihat Detail">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.badges.edit', $badge->id) }}"
                                class="p-2 text-yellow-600 transition-colors rounded-lg hover:bg-yellow-50"
                                title="Edit Badge">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('admin.badges.destroy', $badge->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-2 text-red-600 transition-colors rounded-lg hover:bg-red-50"
                                    title="Hapus Badge"
                                    onclick="return confirm('Yakin ingin menghapus badge {{ $badge->name }}? Semua data terkait akan dihapus.')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <i class="mb-3 text-4xl fa-solid fa-medal"></i>
                            <p class="text-lg font-medium text-gray-600">Tidak ada data badge</p>
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
@if($badges->hasPages())
<div class="mt-6">
    {{ $badges->withQueryString()->links() }}
</div>
@endif
@endsection