@extends('components.admin.layout.app')

@section('header', 'Detail Badge')
@section('subtitle', 'Informasi lengkap tentang badge')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center space-x-3">
            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                <i class="text-xl text-blue-600 fa-solid fa-medal"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Badge</h1>
                <p class="text-gray-600">Informasi lengkap tentang <strong>{{ $badge->name }}</strong></p>
            </div>
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

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
        <!-- Overview Card -->
        <div class="lg:col-span-1">
            <div class="overflow-hidden bg-white rounded-lg shadow-sm border border-gray-100">
                <!-- Badge Header -->
                <div class="p-6 text-center bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="relative inline-block">
                        @if($badge->icon_url)
                            <img src="{{ asset('storage/' . $badge->icon_url) }}"
                                 alt="{{ $badge->name }}"
                                 class="w-32 h-32 mx-auto rounded-full shadow-lg border-4 border-white object-cover">
                        @else
                            <div class="flex items-center justify-center w-32 h-32 mx-auto text-4xl font-bold text-white bg-gradient-to-r from-blue-500 to-purple-600 rounded-full shadow-lg border-4 border-white">
                                <i class="fa-solid fa-medal"></i>
                            </div>
                        @endif
                    </div>

                    <h3 class="mt-4 text-xl font-bold text-gray-800">{{ $badge->name }}</h3>
                    <p class="text-gray-600">{{ $badge->category->name ?? 'Tidak ada kategori' }}</p>

                    <!-- XP Required Badge -->
                    @if($badge->xp_required)
                    <span class="inline-flex items-center px-4 py-2 mt-3 text-sm font-medium text-orange-800 bg-orange-100 border border-orange-200 rounded-full">
                        <i class="mr-2 fa-solid fa-star"></i>
                        {{ number_format($badge->xp_required) }} XP
                    </span>
                    @else
                    <span class="inline-flex items-center px-4 py-2 mt-3 text-sm font-medium text-gray-800 bg-gray-100 border border-gray-200 rounded-full">
                        <i class="mr-2 fa-solid fa-infinity"></i> Tanpa Syarat XP
                    </span>
                    @endif
                </div>

                <!-- Stats -->
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Total Users -->
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-2">
                                <i class="mr-2 text-green-500 fa-solid fa-users"></i>
                                <span class="text-2xl font-bold text-gray-800">{{ $users->count() }}</span>
                            </div>
                            <p class="text-sm text-gray-600">Total Pengguna</p>
                        </div>

                        <!-- Awarded Date Range -->
                        @if($users->count() > 0)
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-2">
                                <i class="mr-2 text-blue-500 fa-solid fa-calendar"></i>
                                <span class="text-sm font-medium text-gray-800">
                                    {{ $users->min('pivot.awarded_at')->format('d M Y') }} –
                                    {{ $users->max('pivot.awarded_at')->format('d M Y') }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">Periode Pemberian</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Badge Details -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <!-- Tabs Navigation -->
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button type="button" data-tab="info"
                            class="tab-button flex-1 py-4 px-6 text-center border-b-2 border-blue-500 text-blue-600 font-medium">
                            <i class="mr-2 fa-solid fa-info-circle"></i>Informasi Badge
                        </button>
                        <button type="button" data-tab="users"
                            class="tab-button flex-1 py-4 px-6 text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium">
                            <i class="mr-2 fa-solid fa-users"></i>Pengguna
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
                            <i class="mr-2 fa-solid fa-id-card"></i>Informasi Detail Badge
                        </h3>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <!-- Basic Information -->
                            <div class="space-y-4">
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i class="w-8 h-8 p-2 mr-3 text-blue-600 bg-blue-100 rounded-lg fa-solid fa-id-badge"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">ID Badge</p>
                                        <p class="font-medium text-gray-900">{{ $badge->id }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i class="w-8 h-8 p-2 mr-3 text-green-600 bg-green-100 rounded-lg fa-solid fa-heading"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">Nama Badge</p>
                                        <p class="font-medium text-gray-900">{{ $badge->name }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i class="w-8 h-8 p-2 mr-3 text-purple-600 bg-purple-100 rounded-lg fa-solid fa-tags"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">Kategori</p>
                                        <p class="font-medium text-gray-900">{{ $badge->category->name ?? 'Tidak ada kategori' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="space-y-4">
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i class="w-8 h-8 p-2 mr-3 text-orange-600 bg-orange-100 rounded-lg fa-solid fa-star"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">XP yang Dibutuhkan</p>
                                        <p class="font-medium text-gray-900">
                                            @if($badge->xp_required)
                                                {{ number_format($badge->xp_required) }} XP
                                            @else
                                                <span class="text-gray-500">Tidak ada syarat</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i class="w-8 h-8 p-2 mr-3 text-indigo-600 bg-indigo-100 rounded-lg fa-solid fa-medal"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">Total Pengguna</p>
                                        <p class="font-medium text-gray-900">{{ $users->count() }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i class="w-8 h-8 p-2 mr-3 text-gray-600 bg-gray-100 rounded-lg fa-solid fa-clock"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">Dibuat pada</p>
                                        <p class="font-medium text-gray-900">{{ $badge->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description Section -->
                        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                            <div class="flex items-start">
                                <i class="mt-1 mr-3 text-blue-600 fa-solid fa-align-left"></i>
                                <div>
                                    <h4 class="font-medium text-blue-900">Deskripsi Badge</h4>
                                    <p class="mt-2 text-sm text-blue-700 whitespace-pre-line">{{ $badge->description }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Ikon Section -->
                        {{-- @if($badge->icon_url)
                        <div class="mt-6 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                            <div class="flex items-center mb-3">
                                <i class="mr-2 text-purple-600 fa-solid fa-image"></i>
                                <h4 class="font-semibold text-purple-800">Ikon Badge</h4>
                            </div>
                            <div class="flex justify-center">
                                <img src="{{ asset('storage/' . $badge->icon_url) }}"
                                     alt="{{ $badge->name }}"
                                     class="w-24 h-24 object-cover rounded-full shadow-md border-2 border-white">
                            </div>
                            <p class="mt-2 text-sm text-purple-700 text-center">
                                Path: <code class="bg-white px-1 rounded">{{ $badge->icon_url }}</code>
                            </p>
                        </div>
                        @endif --}}
                    </div>

                    <!-- Users Tab -->
                    <div id="users-tab" class="hidden tab-content">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">
                            <i class="mr-2 fa-solid fa-users"></i>Daftar Pengguna yang Memiliki Badge Ini
                        </h3>

                        @if($users->count() > 0)
                        <div class="overflow-hidden border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            <i class="mr-1 fa-solid fa-user"></i> Pengguna
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            <i class="mr-1 fa-solid fa-calendar"></i> Tanggal Diberikan
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            <i class="mr-1 fa-solid fa-trophy"></i> Level Saat Diberikan
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($users as $user)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($user->avatar_url)
                                                <img class="w-8 h-8 rounded-full" src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                                                @else
                                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                                @endif
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $user->pivot->awarded_at->format('d M Y H:i') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-indigo-800 bg-indigo-100 rounded-full">
                                                <i class="mr-1 fa-solid fa-level-up-alt"></i>
                                                Level {{ $user->level }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="mb-4 text-4xl fa-solid fa-users-slash"></i>
                            <p>Belum ada pengguna yang memiliki badge ini</p>
                        </div>
                        @endif
                    </div>

                    <!-- Stats Tab -->
                    <div id="stats-tab" class="hidden tab-content">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">
                            <i class="mr-2 fa-solid fa-chart-bar"></i>Statistik Badge
                        </h3>

                        <div class="grid grid-cols-2 gap-6 md:grid-cols-4">
                            <!-- Total Users -->
                            <div class="p-4 text-center bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ $users->count() }}</div>
                                <div class="text-sm font-medium text-blue-800">Total Pengguna</div>
                                <i class="mt-2 text-blue-500 fa-solid fa-users"></i>
                            </div>

                            <!-- XP Required -->
                            <div class="p-4 text-center bg-orange-50 border border-orange-200 rounded-lg">
                                <div class="text-2xl font-bold text-orange-600">
                                    @if($badge->xp_required)
                                        {{ number_format($badge->xp_required) }}
                                    @else
                                        ∞
                                    @endif
                                </div>
                                <div class="text-sm font-medium text-orange-800">XP Dibutuhkan</div>
                                <i class="mt-2 text-orange-500 fa-solid fa-star"></i>
                            </div>

                            <!-- Category -->
                            <div class="p-4 text-center bg-purple-50 border border-purple-200 rounded-lg">
                                <div class="text-sm font-medium text-purple-800">Kategori</div>
                                <div class="mt-1 font-semibold text-purple-600">
                                    {{ $badge->category->name ?? 'Umum' }}
                                </div>
                                <i class="mt-2 text-purple-500 fa-solid fa-tag"></i>
                            </div>

                            <!-- Creation Date -->
                            <div class="p-4 text-center bg-gray-50 border border-gray-200 rounded-lg">
                                <div class="text-sm font-medium text-gray-800">Dibuat</div>
                                <div class="mt-1 text-sm font-semibold text-gray-600">
                                    {{ $badge->created_at->format('d M Y') }}
                                </div>
                                <i class="mt-2 text-gray-500 fa-solid fa-clock"></i>
                            </div>
                        </div>

                        <!-- User Distribution -->
                        @if($users->count() > 0)
                        <div class="mt-6 p-6 bg-gray-50 border border-gray-200 rounded-lg">
                            <h4 class="mb-4 font-semibold text-gray-800">Distribusi Pengguna</h4>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div>
                                    <p class="text-sm text-gray-600">Pengguna Terbanyak</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ $users->groupBy('id')->count() }} pengguna unik
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Rata-rata Level</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ number_format($users->avg('level'), 1) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Pemberian Terakhir</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ $users->max('pivot.awarded_at')->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col gap-3 mt-6 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.badges.index') }}"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-gray-700 transition-colors bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">
                    <i class="mr-2 fa-solid fa-arrow-left"></i> Kembali ke Daftar
                </a>
                <a href="{{ route('admin.badges.edit', $badge->id) }}"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white transition-colors bg-yellow-600 rounded-lg hover:bg-yellow-700">
                    <i class="mr-2 fa-solid fa-edit"></i> Edit Badge
                </a>
                <form action="{{ route('admin.badges.destroy', $badge->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white transition-colors bg-red-600 rounded-lg hover:bg-red-700"
                        onclick="return confirm('Yakin ingin menghapus badge {{ $badge->name }}? Semua data terkait akan dihapus.')">
                        <i class="mr-2 fa-solid fa-trash"></i> Hapus Badge
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

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
@endsection
