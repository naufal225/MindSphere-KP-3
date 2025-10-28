@extends('components.admin.layout.app')

@section('header', 'Detail Tantangan')
@section('subtitle', 'Informasi lengkap tentang tantangan')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center space-x-3">
            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                <i class="text-xl text-blue-600 fa-solid fa-flag"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Tantangan</h1>
                <p class="text-gray-600">Informasi lengkap tentang {{ $challenge->title }}</p>
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
                <!-- Challenge Header -->
                <div class="p-6 text-center bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="relative inline-block">
                        <div
                            class="flex items-center justify-center w-32 h-32 mx-auto text-4xl font-bold text-white bg-gradient-to-r from-blue-500 to-purple-600 rounded-full shadow-lg border-4 border-white">
                            <i class="fa-solid fa-flag"></i>
                        </div>
                        <!-- Status Indicator -->
                        @php
                        $now = now();
                        $statusColor = '';
                        $statusIcon = '';
                        if ($now->between($challenge->start_date, $challenge->end_date)) {
                        $statusColor = 'bg-green-500';
                        $statusIcon = 'fa-play-circle';
                        } elseif ($now->lt($challenge->start_date)) {
                        $statusColor = 'bg-blue-500';
                        $statusIcon = 'fa-clock';
                        } else {
                        $statusColor = 'bg-gray-500';
                        $statusIcon = 'fa-check-circle';
                        }
                        @endphp
                        <div
                            class="absolute bottom-0 right-0 flex items-center justify-center w-8 h-8 {{ $statusColor }} border-4 border-white rounded-full">
                            <i class="text-white fa-solid {{ $statusIcon }} text-xs"></i>
                        </div>
                    </div>

                    <h3 class="mt-4 text-xl font-bold text-gray-800">{{ $challenge->title }}</h3>
                    <p class="text-gray-600">{{ $challenge->category->name ?? 'No Category' }}</p>

                    <!-- Type Badge -->
                    <span
                        class="inline-flex items-center px-4 py-2 mt-3 text-sm font-medium text-purple-800 bg-purple-100 border border-purple-200 rounded-full">
                        <i class="mr-2 fa-solid fa-users"></i>
                        {{ ucfirst($challenge->type->value) }}
                    </span>
                </div>

                <!-- Stats -->
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- XP Reward -->
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-2">
                                <i class="mr-2 text-yellow-500 fa-solid fa-star"></i>
                                <span class="text-2xl font-bold text-gray-800">{{ number_format($challenge->xp_reward)
                                    }}</span>
                            </div>
                            <p class="text-sm text-gray-600">XP Reward</p>
                        </div>

                        <!-- Participants Stats -->
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-2">
                                <i class="mr-2 text-green-500 fa-solid fa-users"></i>
                                <span class="text-2xl font-bold text-gray-800">{{ $challenge->participants->count()
                                    }}</span>
                            </div>
                            <p class="text-sm text-gray-600">Total Partisipan</p>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-4">
                            @php
                            $totalDays = $challenge->start_date->diffInDays($challenge->end_date);
                            $daysPassed = $challenge->start_date->diffInDays(min($now, $challenge->end_date));
                            $progress = $totalDays > 0 ? ($daysPassed / $totalDays) * 100 : 0;
                            $progress = min($progress, 100);
                            @endphp
                            <div class="flex justify-between mb-1 text-xs text-gray-600">
                                <span>Progress Tantangan</span>
                                <span>{{ number_format($progress, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-green-400 to-blue-500 h-2 rounded-full"
                                    style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Challenge Details -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <!-- Tabs Navigation -->
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button type="button" data-tab="info"
                            class="tab-button flex-1 py-4 px-6 text-center border-b-2 border-blue-500 text-blue-600 font-medium">
                            <i class="mr-2 fa-solid fa-info-circle"></i>Informasi Tantangan
                        </button>
                        <button type="button" data-tab="participants"
                            class="tab-button flex-1 py-4 px-6 text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium">
                            <i class="mr-2 fa-solid fa-users"></i>Partisipan
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
                            <i class="mr-2 fa-solid fa-id-card"></i>Informasi Detail Tantangan
                        </h3>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <!-- Basic Information -->
                            <div class="space-y-4">
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i
                                        class="w-8 h-8 p-2 mr-3 text-blue-600 bg-blue-100 rounded-lg fa-solid fa-id-badge"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">ID Tantangan</p>
                                        <p class="font-medium text-gray-900">{{ $challenge->id }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i
                                        class="w-8 h-8 p-2 mr-3 text-green-600 bg-green-100 rounded-lg fa-solid fa-heading"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">Judul Tantangan</p>
                                        <p class="font-medium text-gray-900">{{ $challenge->title }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i
                                        class="w-8 h-8 p-2 mr-3 text-purple-600 bg-purple-100 rounded-lg fa-solid fa-tags"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">Kategori</p>
                                        <p class="font-medium text-gray-900">{{ $challenge->category->name ?? 'Tidak ada
                                            kategori' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="space-y-4">
                               
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i
                                        class="w-8 h-8 p-2 mr-3 text-orange-600 bg-orange-100 rounded-lg fa-solid fa-calendar-plus"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">Dibuat Oleh</p>
                                        <p class="font-medium text-gray-900">{{ $challenge->createdBy->name ?? 'System'
                                            }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i
                                        class="w-8 h-8 p-2 mr-3 text-indigo-600 bg-indigo-100 rounded-lg fa-solid fa-star"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">XP Reward</p>
                                        <p class="font-medium text-gray-900">{{ number_format($challenge->xp_reward) }}
                                            XP</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description Section -->
                        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                            <div class="flex items-start">
                                <i class="mt-1 mr-3 text-blue-600 fa-solid fa-align-left"></i>
                                <div>
                                    <h4 class="font-medium text-blue-900">Deskripsi Tantangan</h4>
                                    <p class="mt-2 text-sm text-blue-700 whitespace-pre-line">{{ $challenge->description
                                        }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline Section -->
                        <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center mb-3">
                                <i class="mr-2 text-green-600 fa-solid fa-calendar-alt"></i>
                                <h4 class="font-semibold text-green-800">Periode Tantangan</h4>
                            </div>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div class="text-center">
                                    <p class="text-sm text-green-600">Tanggal Mulai</p>
                                    <p class="font-semibold text-green-800">{{ $challenge->start_date->format('d M Y')
                                        }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm text-green-600">Tanggal Berakhir</p>
                                    <p class="font-semibold text-green-800">{{ $challenge->end_date->format('d M Y') }}
                                    </p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm text-green-600">Status</p>
                                    @if($now->between($challenge->start_date, $challenge->end_date))
                                    <span class="font-semibold text-green-800">Aktif</span>
                                    @elseif($now->lt($challenge->start_date))
                                    <span class="font-semibold text-blue-800">Akan Datang</span>
                                    @else
                                    <span class="font-semibold text-gray-800">Berakhir</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Participants Tab -->
                    <div id="participants-tab" class="hidden tab-content">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">
                            <i class="mr-2 fa-solid fa-users"></i>Daftar Partisipan
                        </h3>

                        @if($participants->count() > 0)
                        <div class="overflow-hidden border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Nama</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Tanggal Bergabung</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Bukti</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($participants as $participant)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($participant->user->avatar_url)
                                                <img class="w-8 h-8 rounded-full"
                                                    src="{{ $participant->user->avatar_url }}"
                                                    alt="{{ $participant->user->name }}">
                                                @else
                                                <div
                                                    class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                                    {{ substr($participant->user->name, 0, 1) }}
                                                </div>
                                                @endif
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{
                                                        $participant->user->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $participant->user->email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                            $statusColors = [
                                            'joined' => 'bg-blue-100 text-blue-800',
                                            'submitted' => 'bg-yellow-100 text-yellow-800',
                                            'completed' => 'bg-green-100 text-green-800'
                                            ];
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$participant->status->value] }}">
                                                {{ ucfirst($participant->status->value) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $participant->created_at->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            @if($participant->proof_url)
                                            <a href="{{ $participant->proof_url }}" target="_blank"
                                                class="text-blue-600 hover:text-blue-800">
                                                <i class="fa-solid fa-external-link"></i> Lihat Bukti
                                            </a>
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
                            <i class="mb-4 text-4xl fa-solid fa-users-slash"></i>
                            <p>Belum ada partisipan untuk tantangan ini</p>
                        </div>
                        @endif
                    </div>

                    <!-- Stats Tab -->
                    <div id="stats-tab" class="hidden tab-content">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">
                            <i class="mr-2 fa-solid fa-chart-bar"></i>Statistik Tantangan
                        </h3>

                        <div class="grid grid-cols-2 gap-6 md:grid-cols-4">
                            <!-- Total Participants -->
                            <div class="p-4 text-center bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ $participants->count() }}</div>
                                <div class="text-sm font-medium text-blue-800">Total Partisipan</div>
                                <i class="mt-2 text-blue-500 fa-solid fa-users"></i>
                            </div>

                            <!-- Completed -->
                            <div class="p-4 text-center bg-green-50 border border-green-200 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ $participants->where('status',
                                    \App\Enums\ChallengeStatus::COMPLETED)->count() }}</div>
                                <div class="text-sm font-medium text-green-800">Selesai</div>
                                <i class="mt-2 text-green-500 fa-solid fa-check-circle"></i>
                            </div>

                            <!-- Submitted -->
                            <div class="p-4 text-center bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600">{{ $participants->where('status',
                                    \App\Enums\ChallengeStatus::SUBMITTED)->count() }}</div>
                                <div class="text-sm font-medium text-yellow-800">Terkumpul</div>
                                <i class="mt-2 text-yellow-500 fa-solid fa-paper-plane"></i>
                            </div>

                            <!-- Joined -->
                            <div class="p-4 text-center bg-purple-50 border border-purple-200 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600">{{ $participants->where('status',
                                    \App\Enums\ChallengeStatus::JOINED)->count() }}</div>
                                <div class="text-sm font-medium text-purple-800">Bergabung</div>
                                <i class="mt-2 text-purple-500 fa-solid fa-user-plus"></i>
                            </div>
                        </div>

                        <!-- Completion Rate -->
                        <div class="mt-6 p-6 bg-gray-50 border border-gray-200 rounded-lg">
                            <h4 class="mb-4 font-semibold text-gray-800">Tingkat Penyelesaian</h4>
                            @php
                            $total = $participants->count();
                            $completed = $participants->where('status', \App\Enums\ChallengeStatus::COMPLETED)->count();
                            $completionRate = $total > 0 ? ($completed / $total) * 100 : 0;
                            @endphp
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-600">Progress Penyelesaian</span>
                                <span class="text-sm font-medium text-gray-900">{{ number_format($completionRate, 1)
                                    }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-green-500 h-3 rounded-full" style="width: {{ $completionRate }}%"></div>
                            </div>
                            <p class="mt-2 text-sm text-gray-600">{{ $completed }} dari {{ $total }} partisipan telah
                                menyelesaikan tantangan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col gap-3 mt-6 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.challenges.index') }}"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-gray-700 transition-colors bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">
                    <i class="mr-2 fa-solid fa-arrow-left"></i> Kembali ke Daftar
                </a>
                <a href="{{ route('admin.challenges.edit', $challenge->id) }}"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white transition-colors bg-yellow-600 rounded-lg hover:bg-yellow-700">
                    <i class="mr-2 fa-solid fa-edit"></i> Edit Tantangan
                </a>
                <form action="{{ route('admin.challenges.destroy', $challenge->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white transition-colors bg-red-600 rounded-lg hover:bg-red-700"
                        onclick="return confirm('Yakin ingin menghapus tantangan {{ $challenge->title }}? Tindakan ini tidak dapat dibatalkan.')">
                        <i class="mr-2 fa-solid fa-trash"></i> Hapus Tantangan
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
