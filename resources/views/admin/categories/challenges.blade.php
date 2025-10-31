@extends('components.admin.layout.app')

@section('header', 'Challenges Kategori')
@section('subtitle', 'Daftar challenges dalam kategori ' . $category->name)

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg">
                    <i class="text-xl text-purple-600 fa-solid fa-flag"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Challenges Kategori</h1>
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
            <div class="text-2xl font-bold text-purple-600">{{ $challenges->total() }}</div>
            <div class="text-sm font-medium text-gray-600">Total Challenges</div>
        </div>
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="text-2xl font-bold text-green-600">
                {{ $challenges->where('type', 'individual')->count() }}
            </div>
            <div class="text-sm font-medium text-gray-600">Individual</div>
        </div>
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="text-2xl font-bold text-blue-600">
                {{ $challenges->where('type', 'group')->count() }}
            </div>
            <div class="text-sm font-medium text-gray-600">Group</div>
        </div>
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="text-2xl font-bold text-orange-600">
                {{ $challenges->where('end_date', '>=', now())->count() }}
            </div>
            <div class="text-sm font-medium text-gray-600">Aktif</div>
        </div>
    </div>

    <!-- Challenges Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="p-6">
            @if($challenges->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                Judul Challenge
                            </th>
                            <th
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                Tipe
                            </th>
                            <th
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                XP Reward
                            </th>
                            <th
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                Periode
                            </th>
                            <th
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                Status
                            </th>
                            <th
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($challenges as $challenge)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <i class="text-purple-600 fa-solid fa-flag"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $challenge->title }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ Str::limit($challenge->description, 50) }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full
                                    {{ $challenge->type === App\Enums\ChallengeType::SELF ? 'text-green-800 bg-green-100' : 'text-blue-800 bg-blue-100' }}">
                                    {{ $challenge->type === App\Enums\ChallengeType::SELF ? 'Mandiri' : 'Ditugaskan'
                                    }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2 py-1 text-sm font-medium text-orange-800 bg-orange-100 rounded-full">
                                    <i class="mr-1 fa-solid fa-star"></i>
                                    {{ $challenge->xp_reward }} XP
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                <div>{{ $challenge->start_date->format('d M Y') }}</div>
                                <div>s/d {{ $challenge->end_date->format('d M Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                $isActive = $challenge->end_date >= now();
                                $isUpcoming = $challenge->start_date > now();
                                @endphp
                                <span
                                    class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full
                                    {{ $isActive ? 'text-green-800 bg-green-100' : ($isUpcoming ? 'text-blue-800 bg-blue-100' : 'text-red-800 bg-red-100') }}">
                                    {{ $isActive ? 'Aktif' : ($isUpcoming ? 'Akan Datang' : 'Selesai') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                <a href="{{ route('admin.challenges.show', $challenge->id) }}"
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
                {{ $challenges->links() }}
            </div>
            @else
            <div class="text-center py-12">
                <i class="mx-auto text-4xl text-gray-300 fa-solid fa-flag"></i>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak ada challenges</h3>
                <p class="mt-2 text-gray-500">Belum ada challenges dalam kategori ini.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
