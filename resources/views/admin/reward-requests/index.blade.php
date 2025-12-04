@extends('components.admin.layout.app')

@section('header', 'Reward Requests')
@section('subtitle', 'Kelola permintaan penukaran reward dari siswa')

@section('content')
<div class="container mx-auto">
    <!-- Header dengan Statistik -->
    <div class="mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div class="mb-4 lg:mb-0">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg">
                        <i class="text-xl text-white fa-solid fa-hand-paper"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Reward Requests</h1>
                        <p class="text-gray-600">Kelola semua permintaan penukaran reward</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <!-- Export Button -->
                <a href="{{ route('admin.requests.export') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="mr-2 fa-solid fa-download"></i> Export
                </a>
                <!-- Refresh Button -->
                <button onclick="window.location.reload()" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="mr-2 fa-solid fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Requests</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $statistics['total_requests'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="text-blue-600 fa-solid fa-inbox"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $statistics['pending_requests'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="text-yellow-600 fa-solid fa-clock"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Disetujui</p>
                    <p class="text-2xl font-bold text-green-600">{{ $statistics['approved_requests'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="text-green-600 fa-solid fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Koin</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($statistics['total_coin_used'] ?? 0) }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="text-purple-600 fa-solid fa-coins"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="mb-6 bg-white rounded-lg border border-gray-200 p-4">
        <form method="GET" action="{{ route('admin.requests.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                <!-- Tipe Reward -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Reward</label>
                    <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Tipe</option>
                        <option value="physical" {{ request('type') == 'physical' ? 'selected' : '' }}>Fisik</option>
                        <option value="digital" {{ request('type') == 'digital' ? 'selected' : '' }}>Digital</option>
                        <option value="voucher" {{ request('type') == 'voucher' ? 'selected' : '' }}>Voucher</option>
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Search -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari berdasarkan nama siswa, NIS, nama reward, atau kode..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex items-end space-x-2">
                    <button type="submit"
                            class="w-full px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="mr-2 fa-solid fa-search"></i> Filter
                    </button>
                    <a href="{{ route('admin.requests.index') }}"
                       class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Request Table -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        @if($requests->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reward</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Koin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($requests as $request)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #{{ str_pad($request->id, 6, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($request->user->avatar_url)
                                            <img class="h-10 w-10 rounded-full" src="{{ Storage::url($request->user->avatar_url) }}" alt="{{ $request->user->name }}">
                                        @else
                                            <div class="h-10 w-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                                                {{ substr($request->user->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $request->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $request->user->nis ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($request->reward->image_url)
                                            <img class="h-10 w-10 rounded-lg object-cover" src="{{ Storage::url($request->reward->image_url) }}" alt="{{ $request->reward->name }}">
                                        @else
                                            <div class="h-10 w-10 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg flex items-center justify-center">
                                                <i class="text-white fa-solid fa-gift"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $request->reward->name }}</div>
                                        <div class="text-sm text-gray-500">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $request->reward->type_color }}-100 text-{{ $request->reward->type_color }}-800">
                                                {{ $request->reward->type_label }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $request->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <i class="mr-1 text-yellow-500 fa-solid fa-coins"></i>
                                    {{ number_format($request->total_coin_cost) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'approved' => 'bg-green-100 text-green-800 border-green-200',
                                        'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                        'completed' => 'bg-blue-100 text-blue-800 border-blue-200'
                                    ];
                                    $colorClass = $statusColors[$request->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border {{ $colorClass }}">
                                    @if($request->status == 'pending')
                                        <i class="mr-1 fa-solid fa-clock"></i>
                                    @elseif($request->status == 'approved')
                                        <i class="mr-1 fa-solid fa-check-circle"></i>
                                    @elseif($request->status == 'rejected')
                                        <i class="mr-1 fa-solid fa-times-circle"></i>
                                    @elseif($request->status == 'completed')
                                        <i class="mr-1 fa-solid fa-check-double"></i>
                                    @endif
                                    {{ $request->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $request->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.requests.show', $request->id) }}"
                                       class="text-blue-600 hover:text-blue-900" title="Lihat Detail">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @if($request->status == 'pending')
                                        <form action="{{ route('admin.requests.approve', $request->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="text-green-600 hover:text-green-900"
                                                    onclick="return confirm('Setujui request ini?')"
                                                    title="Setujui">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                        <button type="button"
                                                onclick="showRejectModal({{ $request->id }})"
                                                class="text-red-600 hover:text-red-900"
                                                title="Tolak">
                                            <i class="fa-solid fa-times"></i>
                                        </button>
                                    @elseif($request->status == 'approved' && $request->reward->type == 'physical')
                                        <form action="{{ route('admin.requests.complete', $request->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="text-blue-600 hover:text-blue-900"
                                                    onclick="return confirm('Tandai request sebagai selesai?')"
                                                    title="Selesaikan">
                                                <i class="fa-solid fa-check-double"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $requests->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="text-4xl text-gray-400 fa-solid fa-inbox mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data</h3>
                <p class="text-gray-500 mb-4">Belum ada reward requests yang ditemukan.</p>
                @if(request()->hasAny(['status', 'search', 'date_from', 'date_to']))
                    <a href="{{ route('admin.requests.index') }}" class="text-blue-600 hover:text-blue-800">
                        Reset filter
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="mb-4">
            <h3 class="text-lg font-medium text-gray-900">Tolak Reward Request</h3>
            <p class="text-sm text-gray-500">Berikan alasan penolakan</p>
        </div>

        <form id="rejectForm" method="POST" action="">
            @csrf
            <div class="mb-4">
                <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                <textarea id="rejection_reason" name="rejection_reason" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Masukkan alasan penolakan..."
                          required></textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button"
                        onclick="hideRejectModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                    Tolak Request
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showRejectModal(requestId) {
        const form = document.getElementById('rejectForm');
        form.action = "{{ route('admin.requests.reject', "+requestId+") }}/";
        document.getElementById('rejectModal').classList.remove('hidden');
        document.getElementById('rejection_reason').focus();
    }

    function hideRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('rejection_reason').value = '';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('rejectModal');
        if (event.target === modal) {
            hideRejectModal();
        }
    }
</script>
@endpush
