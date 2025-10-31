@extends('components.admin.layout.app')

@section('header', 'Detail Kategori')
@section('subtitle', 'Informasi lengkap tentang kategori')

@section('content')
@php
    $codeLabel = config('category.codes', [])[$category->code] ?? $category->code;
@endphp
<div class="max-w-6xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center space-x-3">
            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                <i class="text-xl text-blue-600 fa-solid fa-tags"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Kategori</h1>
                <p class="text-gray-600">Informasi lengkap tentang {{ $category->name }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
        <!-- Overview Card -->
        <div class="lg:col-span-1">
            <div class="overflow-hidden bg-white rounded-lg shadow-sm border border-gray-100">
                <!-- Category Header -->
                <div class="p-6 text-center bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="relative inline-block">
                        @php
                        $codeColors = [
                            'SA' => 'bg-blue-500',
                            'SI' => 'bg-green-500',
                            'GM' => 'bg-purple-500',
                            'KL' => 'bg-orange-500',
                            'KR' => 'bg-red-500'
                        ];
                        @endphp
                        <div class="flex items-center justify-center w-32 h-32 mx-auto text-4xl font-bold text-white bg-gradient-to-r from-blue-500 to-purple-600 rounded-full shadow-lg border-4 border-white">
                            {{ $category->code }}
                        </div>
                        <!-- Status Indicator -->
                        <div class="absolute bottom-0 right-0 flex items-center justify-center w-8 h-8 bg-green-500 border-4 border-white rounded-full">
                            <i class="text-white fa-solid fa-check text-xs"></i>
                        </div>
                    </div>

                    <h3 class="mt-4 text-xl font-bold text-gray-800">{{ $category->name }}</h3>
                    <p class="text-gray-600">{{ $codeLabel }}</p>

                    <!-- Code Badge -->
                    <span class="inline-flex items-center px-4 py-2 mt-3 text-sm font-medium text-blue-800 bg-blue-100 border border-blue-200 rounded-full">
                        <i class="mr-2 fa-solid fa-code"></i>
                        {{ $category->code }}
                    </span>
                </div>

                <!-- Stats -->
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Habits Stats -->
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-2">
                                <i class="mr-2 text-green-500 fa-solid fa-repeat"></i>
                                <span class="text-2xl font-bold text-gray-800">{{ $category->habits_count }}</span>
                            </div>
                            <p class="text-sm text-gray-600">Total Habits</p>
                        </div>

                        <!-- Challenges Stats -->
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-2">
                                <i class="mr-2 text-purple-500 fa-solid fa-flag"></i>
                                <span class="text-2xl font-bold text-gray-800">{{ $category->challenges_count }}</span>
                            </div>
                            <p class="text-sm text-gray-600">Total Challenges</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Category Details -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <!-- Tabs Navigation -->
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button type="button" data-tab="info"
                            class="tab-button flex-1 py-4 px-6 text-center border-b-2 border-blue-500 text-blue-600 font-medium">
                            <i class="mr-2 fa-solid fa-info-circle"></i>Informasi Kategori
                        </button>
                        <button type="button" data-tab="content"
                            class="tab-button flex-1 py-4 px-6 text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium">
                            <i class="mr-2 fa-solid fa-list"></i>Konten Terkait
                        </button>
                        <button type="button" data-tab="stats"
                            class="tab-button flex-1 py-4 px-6 text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium">
                            <i class="mr-2 fa-solid fa-chart-bar"></i>Statistik
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Info Tab -->
                    <div id="info-tab" class="tab-content">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">
                            <i class="mr-2 fa-solid fa-id-card"></i>Informasi Detail Kategori
                        </h3>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <!-- Basic Information -->
                            <div class="space-y-4">
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i class="w-8 h-8 p-2 mr-3 text-blue-600 bg-blue-100 rounded-lg fa-solid fa-id-badge"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">ID Kategori</p>
                                        <p class="font-medium text-gray-900">{{ $category->id }}</p>
                                    </div>
                                </div>



                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i class="w-8 h-8 p-2 mr-3 text-purple-600 bg-purple-100 rounded-lg fa-solid fa-heading"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">Nama Kategori</p>
                                        <p class="font-medium text-gray-900">{{ $category->name }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="space-y-4">
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i class="w-8 h-8 p-2 mr-3 text-orange-600 bg-orange-100 rounded-lg fa-solid fa-calendar-plus"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">Dibuat Pada</p>
                                        <p class="font-medium text-gray-900">{{ $category->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i class="w-8 h-8 p-2 mr-3 text-indigo-600 bg-indigo-100 rounded-lg fa-solid fa-calendar-check"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">Terakhir Diupdate</p>
                                        <p class="font-medium text-gray-900">{{ $category->updated_at->format('d M Y H:i') }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i class="w-8 h-8 p-2 mr-3 text-red-600 bg-red-100 rounded-lg fa-solid fa-layer-group"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">Total Konten</p>
                                        <p class="font-medium text-gray-900">{{ $category->habits_count + $category->challenges_count }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description Section -->
                        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                            <div class="flex items-start">
                                <i class="mt-1 mr-3 text-blue-600 fa-solid fa-align-left"></i>
                                <div>
                                    <h4 class="font-medium text-blue-900">Deskripsi Kategori</h4>
                                    <p class="mt-2 text-sm text-blue-700 whitespace-pre-line">{{ $category->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Tab -->
                    <div id="content-tab" class="hidden tab-content">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">
                            <i class="mr-2 fa-solid fa-list"></i>Konten Terkait
                        </h3>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Habits Section -->
                            <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <i class="mr-2 text-green-600 fa-solid fa-repeat"></i>
                                        <h4 class="font-semibold text-green-800">Habits</h4>
                                        <span class="ml-2 text-sm text-green-600 bg-green-100 px-2 py-1 rounded-full">
                                            {{ $category->habits_count }}
                                        </span>
                                    </div>
                                    @if($category->habits_count > 4)
                                    <a href="{{ route('admin.categories.habits', $category->id) }}"
                                       class="text-sm text-green-700 hover:text-green-900 hover:underline">
                                        Lihat Detail
                                    </a>
                                    @endif
                                </div>
                                @if($category->habits->count() > 0)
                                <div class="space-y-2 max-h-60 overflow-y-auto">
                                    @foreach($category->habits as $habit)
                                    <div class="flex items-center justify-between p-2 text-sm bg-white rounded-lg border border-green-100">
                                        <div class="flex items-center">
                                            <i class="mr-2 text-green-500 fa-solid fa-circle-small"></i>
                                            <span class="truncate">{{ $habit->title }}</span>
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $habit->created_at->format('d M') }}</span>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <p class="text-sm text-green-700">Belum ada habits dalam kategori ini</p>
                                @endif
                            </div>

                            <!-- Challenges Section -->
                            <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <i class="mr-2 text-purple-600 fa-solid fa-flag"></i>
                                        <h4 class="font-semibold text-purple-800">Challenges</h4>
                                        <span class="ml-2 text-sm text-purple-600 bg-purple-100 px-2 py-1 rounded-full">
                                            {{ $category->challenges_count }}
                                        </span>
                                    </div>
                                    @if($category->challenges_count > 4)
                                    <a href="{{ route('admin.categories.challenges', $category->id) }}"
                                       class="text-sm text-purple-700 hover:text-purple-900 hover:underline">
                                        Lihat Detail
                                    </a>
                                    @endif
                                </div>
                                @if($category->challenges->count() > 0)
                                <div class="space-y-2 max-h-60 overflow-y-auto">
                                    @foreach($category->challenges as $challenge)
                                    <div class="flex items-center justify-between p-2 text-sm bg-white rounded-lg border border-purple-100">
                                        <div class="flex items-center">
                                            <i class="mr-2 text-purple-500 fa-solid fa-circle-small"></i>
                                            <span class="truncate">{{ $challenge->title }}</span>
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $challenge->created_at->format('d M') }}</span>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <p class="text-sm text-purple-700">Belum ada challenges dalam kategori ini</p>
                                @endif
                            </div>

                        </div>
                    </div>

                    <!-- Stats Tab -->
                    <div id="stats-tab" class="hidden tab-content">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">
                            <i class="mr-2 fa-solid fa-chart-bar"></i>Statistik Kategori
                        </h3>

                        <div class="grid grid-cols-2 gap-6 md:grid-cols-4">
                            <!-- Total Habits -->
                            <div class="p-4 text-center bg-green-50 border border-green-200 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ $category->habits_count }}</div>
                                <div class="text-sm font-medium text-green-800">Habits</div>
                                <i class="mt-2 text-green-500 fa-solid fa-repeat"></i>
                            </div>

                            <!-- Total Challenges -->
                            <div class="p-4 text-center bg-purple-50 border border-purple-200 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600">{{ $category->challenges_count }}</div>
                                <div class="text-sm font-medium text-purple-800">Challenges</div>
                                <i class="mt-2 text-purple-500 fa-solid fa-flag"></i>
                            </div>


                        </div>

                        <!-- Distribution Chart (Placeholder) -->
                        <div class="mt-6 p-6 bg-gray-50 border border-gray-200 rounded-lg">
                            <h4 class="mb-4 font-semibold text-gray-800">Distribusi Konten</h4>
                            <div class="text-center py-8 text-gray-500">
                                <i class="mb-4 text-4xl fa-solid fa-chart-pie"></i>
                                <p>Grafik distribusi konten akan ditampilkan di sini</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col gap-3 mt-6 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.categories.index') }}"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-gray-700 transition-colors bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">
                    <i class="mr-2 fa-solid fa-arrow-left"></i> Kembali ke Daftar
                </a>
                <a href="{{ route('admin.categories.edit', $category->id) }}"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white transition-colors bg-yellow-600 rounded-lg hover:bg-yellow-700">
                    <i class="mr-2 fa-solid fa-edit"></i> Edit Kategori
                </a>
                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white transition-colors bg-red-600 rounded-lg hover:bg-red-700"
                        onclick="return confirm('Yakin ingin menghapus kategori {{ $category->name }}? Tindakan ini tidak dapat dibatalkan.')">
                        <i class="mr-2 fa-solid fa-trash"></i> Hapus Kategori
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
