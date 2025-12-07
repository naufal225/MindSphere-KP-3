@extends('components.admin.layout.app')

@section('header', 'Detail Reward Request')
@section('subtitle', 'Kelola proses approve/reject/selesai untuk reward request')

@section('content')
<div class="max-w-6xl mx-auto">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">
            {{ session('success') }}
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

    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Info utama -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-sm text-gray-500">Request ID</p>
                        <h2 class="text-2xl font-semibold text-gray-900">#{{ str_pad($request->id, 6, '0', STR_PAD_LEFT) }}</h2>
                        <p class="text-sm text-gray-500 mt-1">Diajukan: {{ $request->created_at->format('d M Y H:i') }}</p>
                    </div>
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
                        {{ $request->status_label }}
                    </span>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="p-4 border border-gray-100 rounded-lg">
                        <h3 class="text-sm font-semibold text-gray-800 mb-2">Member</h3>
                        <div class="flex items-center space-x-3">
                            <div class="h-12 w-12">
                                @if($request->user->avatar_url)
                                    <img class="h-12 w-12 rounded-full object-cover" src="{{ Storage::url($request->user->avatar_url) }}" alt="{{ $request->user->name }}">
                                @else
                                    <div class="h-12 w-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ substr($request->user->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $request->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $request->user->email }}</p>
                                <p class="text-xs text-gray-400">NIS: {{ $request->user->nis ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 border border-gray-100 rounded-lg">
                        <h3 class="text-sm font-semibold text-gray-800 mb-2">Reward</h3>
                        <div class="flex items-center space-x-3">
                            <div class="h-14 w-14 rounded-lg overflow-hidden bg-gray-50 border border-gray-200">
                                @if($request->reward && $request->reward->image_url)
                                    <img class="h-full w-full object-cover" src="{{ Storage::url($request->reward->image_url) }}" alt="{{ $request->reward->name }}">
                                @else
                                    <div class="h-full w-full flex items-center justify-center bg-gradient-to-r from-yellow-500 to-orange-500 text-white">
                                        <i class="fa-solid fa-gift"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $request->reward->name ?? ($request->reward_snapshot['name'] ?? '-') }}</p>
                                <p class="text-sm text-gray-500">Tipe: {{ $request->reward->type_label ?? ($request->reward_snapshot['type'] ?? '-') }}</p>
                                <p class="text-sm text-gray-700 flex items-center">
                                    <i class="mr-1 text-yellow-500 fa-solid fa-coins"></i> {{ number_format($request->total_coin_cost) }} koin
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-3 mt-4">
                    <div class="p-4 border border-gray-100 rounded-lg">
                        <p class="text-sm text-gray-500">Jumlah</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $request->quantity }}</p>
                    </div>
                    <div class="p-4 border border-gray-100 rounded-lg">
                        <p class="text-sm text-gray-500">Kode (jika ada)</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $request->code ?? '-' }}</p>
                        @if($request->code_expires_at)
                            <p class="text-xs text-gray-500">Expire: {{ $request->code_expires_at->format('d M Y H:i') }}</p>
                        @endif
                    </div>
                    <div class="p-4 border border-gray-100 rounded-lg">
                        <p class="text-sm text-gray-500">Alasan Penolakan</p>
                        <p class="text-sm text-gray-900">{{ $request->rejection_reason ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h3>
                <div class="space-y-3 text-sm text-gray-700">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-circle text-blue-500"></i>
                        <span>Dibuat: {{ $request->created_at->format('d M Y H:i') }}</span>
                    </div>
                    @if($request->approved_at)
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-circle text-green-500"></i>
                            <span>Disetujui: {{ $request->approved_at->format('d M Y H:i') }} oleh {{ $request->approver->name ?? '-' }}</span>
                        </div>
                    @endif
                    @if($request->completed_at)
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-circle text-indigo-500"></i>
                            <span>Selesai: {{ $request->completed_at->format('d M Y H:i') }} oleh {{ $request->completer->name ?? '-' }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="space-y-4">
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Aksi</h3>
                @if($request->status === \App\Models\RewardRequest::STATUS_PENDING)
                    <form action="{{ route('admin.requests.approve', $request->id) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700"
                                onclick="return confirm('Setujui request ini?')">
                            <i class="mr-2 fa-solid fa-check"></i> Setujui
                        </button>
                    </form>

                    <button type="button"
                            onclick="document.getElementById('rejectModal').classList.remove('hidden')"
                            class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700">
                        <i class="mr-2 fa-solid fa-times"></i> Tolak
                    </button>
                @elseif($request->status === \App\Models\RewardRequest::STATUS_APPROVED)
                    <form action="{{ route('admin.requests.complete', $request->id) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700"
                                onclick="return confirm('Tandai sebagai selesai?')">
                            <i class="mr-2 fa-solid fa-check-double"></i> Tandai Selesai
                        </button>
                    </form>
                @else
                    <p class="text-sm text-gray-500">Tidak ada aksi yang tersedia untuk status ini.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="mb-4">
            <h3 class="text-lg font-medium text-gray-900">Tolak Reward Request</h3>
            <p class="text-sm text-gray-500">Berikan alasan penolakan</p>
        </div>

        <form method="POST" action="{{ route('admin.requests.reject', $request->id) }}">
            @csrf
            <div class="mb-4">
                <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                <textarea id="rejection_reason" name="rejection_reason" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Masukkan alasan penolakan..." required></textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button"
                        onclick="document.getElementById('rejectModal').classList.add('hidden')"
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
