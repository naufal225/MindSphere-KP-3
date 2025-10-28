@extends('components.admin.layout.app')

@section('header', 'Badges Kategori')
@section('subtitle', 'Daftar badges dalam kategori ' . $category->name)

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-lg">
                    <i class="text-xl text-yellow-600 fa-solid fa-medal"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Badges Kategori</h1>
                    <p class="text-gray-600">{{ $category->name }} - {{ config('category.codes', [])[$category->code] ?? $category->code }}</p>
                </div>
            </div>
            <a href="{{ route('admin.categories.show', $category->id) }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                <i class="mr-2 fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-3">
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="text-2xl font-bold text-yellow-600">{{ $badges->total() }}</div>
            <div class="text-sm font-medium text-gray-600">Total Badges</div>
        </div>
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="text-2xl font-bold text-green-600">
                {{ $badges->where('xp_required', '<=', 1000)->count() }}
            </div>
            <div class="text-sm font-medium text-gray-600">Easy</div>
        </div>
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="text-2xl font-bold text-orange-600">
                {{ $badges->where('xp_required', '>', 1000)->count() }}
            </div>
            <div class="text-sm font-medium text-gray-600">Advanced</div>
        </div>
    </div>

    <!-- Badges Grid -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="p-6">
            @if($badges->count() > 0)
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($badges as $badge)
                <div class="p-4 border border-gray-200 rounded-lg hover:shadow-md transition-shadow">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-16 h-16 bg-yellow-100 rounded-lg">
                                <i class="text-2xl text-yellow-600 fa-solid fa-medal"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $badge->name }}</h3>
                            <p class="mt-1 text-sm text-gray-500 line-clamp-2">{{ $badge->description }}</p>
                            <div class="mt-2">
                                @if($badge->xp_required)
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-orange-800 bg-orange-100 rounded-full">
                                    <i class="mr-1 fa-solid fa-star"></i>
                                    {{ $badge->xp_required }} XP Required
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">
                                    <i class="mr-1 fa-solid fa-gift"></i>
                                    Free Badge
                                </span>
                                @endif
                            </div>
                            <div class="mt-3 text-xs text-gray-400">
                                Dibuat: {{ $badge->created_at->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end mt-4 space-x-2">
                        <a href="{{ route('admin.badges.show', $badge->id) }}"
                           class="inline-flex items-center px-3 py-1 text-sm text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100">
                            <i class="mr-1 fa-solid fa-eye"></i> Detail
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $badges->links() }}
            </div>
            @else
            <div class="text-center py-12">
                <i class="mx-auto text-4xl text-gray-300 fa-solid fa-medal"></i>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak ada badges</h3>
                <p class="mt-2 text-gray-500">Belum ada badges dalam kategori ini.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
