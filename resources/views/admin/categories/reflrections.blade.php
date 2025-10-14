@extends('components.admin.layout.app')

@section('header', 'Reflections Kategori')
@section('subtitle', 'Daftar reflections dalam kategori ' . $category->name)

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                    <i class="text-xl text-blue-600 fa-solid fa-message"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Reflections Kategori</h1>
                    <p class="text-gray-600">{{ $category->name }} - {{ $category->code->name }}</p>
                </div>
            </div>
            <a href="{{ route('admin.categories.show', $category->id) }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                <i class="mr-2 fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-4">
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="text-2xl font-bold text-blue-600">{{ $reflections->total() }}</div>
            <div class="text-sm font-medium text-gray-600">Total Reflections</div>
        </div>
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="text-2xl font-bold text-green-600">
                {{ $reflections->where('is_private', false)->count() }}
            </div>
            <div class="text-sm font-medium text-gray-600">Public</div>
        </div>
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="text-2xl font-bold text-purple-600">
                {{ $reflections->where('is_private', true)->count() }}
            </div>
            <div class="text-sm font-medium text-gray-600">Private</div>
        </div>
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="text-2xl font-bold text-orange-600">
                {{ $reflections->where('date', today())->count() }}
            </div>
            <div class="text-sm font-medium text-gray-600">Hari Ini</div>
        </div>
    </div>

    <!-- Reflections Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="p-6">
            @if($reflections->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                Konten
                            </th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                Mood
                            </th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                Status
                            </th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                Tanggal
                            </th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($reflections as $reflection)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="text-blue-600 fa-solid fa-message"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm text-gray-900 max-w-md">
                                            {{ Str::limit($reflection->content, 100) }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $moodIcons = [
                                        'happy' => ['icon' => 'fa-face-smile', 'color' => 'text-green-600 bg-green-100'],
                                        'neutral' => ['icon' => 'fa-face-meh', 'color' => 'text-yellow-600 bg-yellow-100'],
                                        'sad' => ['icon' => 'fa-face-sad-tear', 'color' => 'text-blue-600 bg-blue-100'],
                                        'angry' => ['icon' => 'fa-face-angry', 'color' => 'text-red-600 bg-red-100'],
                                        'tired' => ['icon' => 'fa-face-tired', 'color' => 'text-gray-600 bg-gray-100']
                                    ];
                                    $moodConfig = $moodIcons[$reflection->mood] ?? $moodIcons['neutral'];
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {{ $moodConfig['color'] }}">
                                    <i class="mr-1 fa-solid {{ $moodConfig['icon'] }}"></i>
                                    {{ ucfirst($reflection->mood) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full
                                    {{ $reflection->is_private ? 'text-purple-800 bg-purple-100' : 'text-green-800 bg-green-100' }}">
                                    {{ $reflection->is_private ? 'Private' : 'Public' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $reflection->date->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                <a href="{{ route('admin.reflections.show', $reflection->id) }}"
                                   class="text-blue-600 hover:text-blue-900">
                                    <i class="fa-solid fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $reflections->links() }}
            </div>
            @else
            <div class="text-center py-12">
                <i class="mx-auto text-4xl text-gray-300 fa-solid fa-message"></i>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak ada reflections</h3>
                <p class="mt-2 text-gray-500">Belum ada reflections dalam kategori ini.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
