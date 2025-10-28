@extends('components.admin.layout.app')

@section('header', 'Habits Kategori')
@section('subtitle', 'Daftar habits dalam kategori ' . $category->name)

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                    <i class="text-xl text-green-600 fa-solid fa-repeat"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Habits Kategori</h1>
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
    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-4">
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="text-2xl font-bold text-green-600">{{ $habits->total() }}</div>
            <div class="text-sm font-medium text-gray-600">Total Habits</div>
        </div>
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="text-2xl font-bold text-blue-600">
                {{ $habits->where('type', 'self')->count() }}
            </div>
            <div class="text-sm font-medium text-gray-600">Self</div>
        </div>
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="text-2xl font-bold text-purple-600">
                {{ $habits->where('type', 'assigned')->count() }}
            </div>
            <div class="text-sm font-medium text-gray-600">Assigned</div>
        </div>
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="text-2xl font-bold text-orange-600">
                {{ $habits->where('period', 'daily')->count() }}
            </div>
            <div class="text-sm font-medium text-gray-600">Daily</div>
        </div>
    </div>

    <!-- Habits Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="p-6">
            @if($habits->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                Judul Habit
                            </th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                Tipe
                            </th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                Periode
                            </th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                Dibuat Pada
                            </th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($habits as $habit)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="text-green-600 fa-solid fa-repeat"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $habit->title }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ Str::limit($habit->description, 50) }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full
                                    {{ $habit->type === 'self' ? 'text-blue-800 bg-blue-100' : 'text-purple-800 bg-purple-100' }}">
                                    {{ $habit->type === 'self' ? 'Self' : 'Assigned' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 text-sm font-medium rounded-full
                                    {{ $habit->period === 'daily' ? 'text-orange-800 bg-orange-100' : 'text-indigo-800 bg-indigo-100' }}">
                                    <i class="mr-1 fa-solid fa-calendar"></i>
                                    {{ $habit->period === 'daily' ? 'Harian' : 'Mingguan' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $habit->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                <a href="{{ route('admin.habits.show', $habit->id) }}"
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
                {{ $habits->links() }}
            </div>
            @else
            <div class="text-center py-12">
                <i class="mx-auto text-4xl text-gray-300 fa-solid fa-repeat"></i>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak ada habits</h3>
                <p class="mt-2 text-gray-500">Belum ada habits dalam kategori ini.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
