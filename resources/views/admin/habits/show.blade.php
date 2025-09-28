@extends('components.admin.layout.app')

@section('header', 'Detail Kebiasaan')
@section('subtitle', 'Informasi lengkap tentang kebiasaan')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center space-x-3">
            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                <i class="text-xl text-blue-600 fa-solid fa-repeat"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Kebiasaan</h1>
                <p class="text-gray-600">Informasi lengkap tentang {{ $habit->title }}</p>
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
                <!-- Habit Header -->
                <div class="p-6 text-center bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="relative inline-block">
                        <div
                            class="flex items-center justify-center w-32 h-32 mx-auto text-4xl font-bold text-white bg-gradient-to-r from-blue-500 to-purple-600 rounded-full shadow-lg border-4 border-white">
                            <i class="fa-solid fa-repeat"></i>
                        </div>
                        <!-- Type Indicator -->
                        @php
                        $typeColor = $habit->type->value === 'assigned' ? 'bg-purple-500' : 'bg-green-500';
                        $typeIcon = $habit->type->value === 'assigned' ? 'fa-user-check' : 'fa-user';
                        @endphp
                        <div
                            class="absolute bottom-0 right-0 flex items-center justify-center w-8 h-8 {{ $typeColor }} border-4 border-white rounded-full">
                            <i class="text-white fa-solid {{ $typeIcon }} text-xs"></i>
                        </div>
                    </div>

                    <h3 class="mt-4 text-xl font-bold text-gray-800">{{ $habit->title }}</h3>
                    <p class="text-gray-600">{{ $habit->category->name ?? 'Tidak ada kategori' }}</p>

                    <!-- Period Badge -->
                    <span
                        class="inline-flex items-center px-4 py-2 mt-3 text-sm font-medium {{ $habit->period->value === 'daily' ? 'text-blue-800 bg-blue-100 border border-blue-200' : 'text-orange-800 bg-orange-100 border border-orange-200' }} rounded-full">
                        <i class="mr-2 fa-solid {{ $habit->period->value === 'daily' ? 'fa-sun' : 'fa-calendar-week' }}"></i>
                        {{ $habit->period->value === 'daily' ? 'Harian' : 'Mingguan' }}
                    </span>
                </div>

                <!-- Stats -->
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Total Logs -->
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-2">
                                <i class="mr-2 text-indigo-500 fa-solid fa-clipboard-list"></i>
                                <span class="text-2xl font-bold text-gray-800">{{ $logs->count() }}</span>
                            </div>
                            <p class="text-sm text-gray-600">Total Pencatatan</p>
                        </div>

                        <!-- Completion Rate -->
                        @php
                        $totalLogs = $logs->count();
                        $completedLogs = $logs->where('status', 'done')->count();
                        $completionRate = $totalLogs > 0 ? ($completedLogs / $totalLogs) * 100 : 0;
                        @endphp
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-2">
                                <i class="mr-2 text-green-500 fa-solid fa-check-circle"></i>
                                <span class="text-2xl font-bold text-gray-800">{{ number_format($completionRate, 1) }}%</span>
                            </div>
                            <p class="text-sm text-gray-600">Tingkat Penyelesaian</p>
                        </div>

                        <!-- Assigned Info -->
                        @if($habit->type->value === 'assigned')
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-2">
                                <i class="mr-2 text-purple-500 fa-solid fa-user-tie"></i>
                                <span class="text-sm font-medium text-gray-800">
                                    {{ $habit->assignedBy?->name ?? 'Tidak diketahui' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">Ditugaskan Oleh</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Habit Details -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <!-- Tabs Navigation -->
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button type="button" data-tab="info"
                            class="tab-button flex-1 py-4 px-6 text-center border-b-2 border-blue-500 text-blue-600 font-medium">
                            <i class="mr-2 fa-solid fa-info-circle"></i>Informasi Kebiasaan
                        </button>
                        <button type="button" data-tab="logs"
                            class="tab-button flex-1 py-4 px-6 text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium">
                            <i class="mr-2 fa-solid fa-clipboard-list"></i>Pencatatan
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
                            <i class="mr-2 fa-solid fa-id-card"></i>Informasi Detail Kebiasaan
                        </h3>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <!-- Basic Information -->
                            <div class="space-y-4">
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i
                                        class="w-8 h-8 p-2 mr-3 text-blue-600 bg-blue-100 rounded-lg fa-solid fa-id-badge"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">ID Kebiasaan</p>
                                        <p class="font-medium text-gray-900">{{ $habit->id }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i
                                        class="w-8 h-8 p-2 mr-3 text-green-600 bg-green-100 rounded-lg fa-solid fa-heading"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">Judul Kebiasaan</p>
                                        <p class="font-medium text-gray-900">{{ $habit->title }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i
                                        class="w-8 h-8 p-2 mr-3 text-purple-600 bg-purple-100 rounded-lg fa-solid fa-tags"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">Kategori</p>
                                        <p class="font-medium text-gray-900">{{ $habit->category->name ?? 'Tidak ada kategori' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="space-y-4">
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i
                                        class="w-8 h-8 p-2 mr-3 text-red-600 bg-red-100 rounded-lg fa-solid fa-user"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">Tipe Kebiasaan</p>
                                        <p class="font-medium text-gray-900">
                                            {{ $habit->type->value === 'self' ? 'Mandiri' : 'Ditugaskan' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i
                                        class="w-8 h-8 p-2 mr-3 text-orange-600 bg-orange-100 rounded-lg fa-solid fa-calendar"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">Periode</p>
                                        <p class="font-medium text-gray-900">
                                            {{ $habit->period->value === 'daily' ? 'Harian' : 'Mingguan' }}
                                        </p>
                                    </div>
                                </div>

                                @if($habit->type->value === 'assigned')
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i
                                        class="w-8 h-8 p-2 mr-3 text-indigo-600 bg-indigo-100 rounded-lg fa-solid fa-user-tie"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">Ditugaskan Oleh</p>
                                        <p class="font-medium text-gray-900">{{ $habit->assignedBy?->name ?? 'Tidak diketahui' }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Description Section -->
                        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                            <div class="flex items-start">
                                <i class="mt-1 mr-3 text-blue-600 fa-solid fa-align-left"></i>
                                <div>
                                    <h4 class="font-medium text-blue-900">Deskripsi Kebiasaan</h4>
                                    <p class="mt-2 text-sm text-blue-700 whitespace-pre-line">{{ $habit->description }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Creation Info -->
                        <div class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <div class="flex items-center mb-3">
                                <i class="mr-2 text-gray-600 fa-solid fa-clock"></i>
                                <h4 class="font-semibold text-gray-800">Informasi Waktu</h4>
                            </div>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <p class="text-sm text-gray-600">Dibuat pada</p>
                                    <p class="font-semibold text-gray-900">{{ $habit->created_at->format('d M Y H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Terakhir diperbarui</p>
                                    <p class="font-semibold text-gray-900">{{ $habit->updated_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Logs Tab -->
                    <div id="logs-tab" class="hidden tab-content">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">
                            <i class="mr-2 fa-solid fa-clipboard-list"></i>Riwayat Pencatatan
                        </h3>

                        @if($logs->count() > 0)
                        <div class="overflow-hidden border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Tanggal</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Pengguna</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($logs as $log)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $log->date->format('d M Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($log->user->avatar_url)
                                                <img class="w-8 h-8 rounded-full" src="{{ $log->user->avatar_url }}" alt="{{ $log->user->name }}">
                                                @else
                                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                                    {{ substr($log->user->name, 0, 1) }}
                                                </div>
                                                @endif
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $log->user->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $log->user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                            $statusColors = [
                                                'done' => 'bg-green-100 text-green-800',
                                                'not_done' => 'bg-red-100 text-red-800'
                                            ];
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$log->status->value] }}">
                                                {{ $log->status->value === 'done' ? 'Selesai' : 'Belum Selesai' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs">
                                            @if($log->note)
                                                <p class="truncate">{{ $log->note }}</p>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="mb-4 text-4xl fa-solid fa-clipboard-list"></i>
                            <p>Belum ada pencatatan untuk kebiasaan ini</p>
                        </div>
                        @endif
                    </div>

                    <!-- Stats Tab -->
                    <div id="stats-tab" class="hidden tab-content">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">
                            <i class="mr-2 fa-solid fa-chart-bar"></i>Statistik Kebiasaan
                        </h3>

                        <div class="grid grid-cols-2 gap-6 md:grid-cols-4">
                            <!-- Total Logs -->
                            <div class="p-4 text-center bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ $logs->count() }}</div>
                                <div class="text-sm font-medium text-blue-800">Total Pencatatan</div>
                                <i class="mt-2 text-blue-500 fa-solid fa-clipboard-list"></i>
                            </div>

                            <!-- Completed -->
                            <div class="p-4 text-center bg-green-50 border border-green-200 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ $completedLogs }}</div>
                                <div class="text-sm font-medium text-green-800">Selesai</div>
                                <i class="mt-2 text-green-500 fa-solid fa-check-circle"></i>
                            </div>

                            <!-- Not Completed -->
                            <div class="p-4 text-center bg-red-50 border border-red-200 rounded-lg">
                                <div class="text-2xl font-bold text-red-600">{{ $logs->count() - $completedLogs }}</div>
                                <div class="text-sm font-medium text-red-800">Belum Selesai</div>
                                <i class="mt-2 text-red-500 fa-solid fa-times-circle"></i>
                            </div>

                            <!-- Completion Rate -->
                            <div class="p-4 text-center bg-purple-50 border border-purple-200 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600">{{ number_format($completionRate, 1) }}%</div>
                                <div class="text-sm font-medium text-purple-800">Tingkat Penyelesaian</div>
                                <i class="mt-2 text-purple-500 fa-solid fa-percentage"></i>
                            </div>
                        </div>

                        <!-- Completion Trend -->
                        <div class="mt-6 p-6 bg-gray-50 border border-gray-200 rounded-lg">
                            <h4 class="mb-4 font-semibold text-gray-800">Tren Penyelesaian</h4>
                            <p class="text-sm text-gray-600">
                                Dari {{ $logs->count() }} pencatatan, {{ $completedLogs }} berhasil diselesaikan.
                            </p>
                            <div class="flex items-center justify-between mt-3">
                                <span class="text-sm text-gray-600">Progress</span>
                                <span class="text-sm font-medium text-gray-900">{{ number_format($completionRate, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 mt-2">
                                <div class="bg-green-500 h-3 rounded-full" style="width: {{ $completionRate }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col gap-3 mt-6 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.habits.index') }}"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-gray-700 transition-colors bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">
                    <i class="mr-2 fa-solid fa-arrow-left"></i> Kembali ke Daftar
                </a>
                <a href="{{ route('admin.habits.edit', $habit->id) }}"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white transition-colors bg-yellow-600 rounded-lg hover:bg-yellow-700">
                    <i class="mr-2 fa-solid fa-edit"></i> Edit Kebiasaan
                </a>
                <form action="{{ route('admin.habits.destroy', $habit->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white transition-colors bg-red-600 rounded-lg hover:bg-red-700"
                        onclick="return confirm('Yakin ingin menghapus kebiasaan {{ $habit->title }}? Semua pencatatan terkait juga akan dihapus.')">
                        <i class="mr-2 fa-solid fa-trash"></i> Hapus Kebiasaan
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
