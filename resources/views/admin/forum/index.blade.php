@extends('components.admin.layout.app')
@section('header', 'Forum Management')
@section('subtitle', 'Kelola semua thread forum')

@section('content')
<main class="relative z-10 flex-1 p-0 space-y-6 overflow-x-hidden overflow-y-auto bg-gray-50">
    <!-- Header Section -->
    <div class="px-6 pt-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900">Forum Management</h1>
                <p class="text-neutral-600">Kelola postingan forum dari pengguna</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mx-6 flex items-center p-4 border border-green-200 bg-green-50 rounded-xl">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Search & Filter -->
    <div class="mx-6 bg-white border rounded-xl shadow-sm border-neutral-200 p-6">
        <form method="GET" class="space-y-4">
            <!-- Search -->
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Cari Postingan</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari berdasarkan judul atau nama pengguna..."
                        class="w-full px-3 py-2.5 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    >
                </div>

                <div class="sm:w-48">
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Scope</label>
                    <select name="scope" class="w-full px-3 py-2.5 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="">Semua</option>
                        <option value="global" {{ request('scope') == 'global' ? 'selected' : '' }}>Global</option>
                        <option value="class" {{ request('scope') == 'class' ? 'selected' : '' }}>Kelas</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors flex items-center">
                    <i class="fas fa-search mr-2"></i> Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Daftar Postingan -->
    @if($forums->count())
        <div class="px-6 space-y-4">
            @foreach($forums as $post)
                @include('admin.forum._partials.post-item', ['post' => $post])
            @endforeach
        </div>

        <div class="px-6 mt-6">
            {{ $forums->appends(request()->query())->links() }}
        </div>
    @else
        <div class="mx-6 bg-white border rounded-xl shadow-sm border-neutral-200 p-12 text-center">
            <div class="text-neutral-400 mb-4">
                <i class="fas fa-comments text-4xl"></i>
            </div>
            <p class="text-neutral-500 text-lg">Tidak ada postingan forum yang ditemukan.</p>
        </div>
    @endif
</main>
@endsection
