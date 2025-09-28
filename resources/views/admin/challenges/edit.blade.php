@extends('components.admin.layout.app')

@section('header', 'Edit Tantangan')
@section('subtitle', 'Perbarui informasi tantangan')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center space-x-3">
            <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-lg">
                <i class="text-xl text-yellow-600 fa-solid fa-flag"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Edit Tantangan</h1>
                <p class="text-gray-600">Perbarui informasi untuk tantangan {{ $challenge->title }}</p>
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

    <div class="p-6 bg-white rounded-lg shadow-sm border border-gray-100">
        <form action="{{ route('admin.challenges.update', $challenge->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-8">
                <!-- Informasi Dasar -->
                <div>
                    <div class="flex items-center mb-4">
                        <i class="mr-2 text-blue-500 fa-solid fa-info-circle"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Informasi Dasar</h3>
                    </div>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Judul Tantangan -->
                        <div class="md:col-span-2">
                            <label for="title" class="block mb-2 text-sm font-medium text-gray-700">
                                <i class="mr-1 fa-solid fa-heading"></i> Judul Tantangan
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title', $challenge->title) }}"
                                required placeholder="Masukkan judul tantangan yang menarik"
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror">
                            @error('title')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="mr-1 fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label for="category_id" class="block mb-2 text-sm font-medium text-gray-700">
                                <i class="mr-1 fa-solid fa-tags"></i> Kategori
                            </label>
                            <select name="category_id" id="category_id" required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('category_id') border-red-500 @enderror">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ (old('category_id') ?? $challenge->category_id)
                                    == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <p class="mt-2 text-sm textred-600">
                                <i class="mr-1 fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <!-- Tipe Tantangan -->
                        <div>
                            <label for="type" class="block mb-2 text-sm font-medium text-gray-700">
                                <i class="mr-1 fa-solid fa-users"></i> Tipe Tantangan
                            </label>
                            <select name="type" id="type" required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-500 @enderror">
                                <option value="">Pilih Tipe</option>
                                @foreach(\App\Enums\ChallengeType::cases() as $type)
                                <option value="{{ $type->value }}" {{ (old('type') ?? $challenge->type->value) ==
                                    $type->value ? 'selected' : '' }}>
                                    {{ ucfirst($type->value) }}
                                </option>
                                @endforeach
                            </select>
                            @error('type')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="mr-1 fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div>
                    <div class="flex items-center mb-4">
                        <i class="mr-2 text-green-500 fa-solid fa-align-left"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Deskripsi Tantangan</h3>
                    </div>
                    <div>
                        <label for="description" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-1 fa-solid fa-file-lines"></i> Deskripsi Detail
                        </label>
                        <textarea name="description" id="description" rows="5" required
                            placeholder="Jelaskan detail tantangan, aturan, dan tujuan yang ingin dicapai..."
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $challenge->description) }}</textarea>
                        @error('description')
                        <p class="mt-2 text-sm text-red-600">
                            <i class="mr-1 fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>
                </div>

                <!-- Reward & Periode -->
                <div>
                    <div class="flex items-center mb-4">
                        <i class="mr-2 text-yellow-500 fa-solid fa-gift"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Reward & Periode</h3>
                    </div>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <!-- XP Reward -->
                        <div>
                            <label for="xp_reward" class="block mb-2 text-sm font-medium text-gray-700">
                                <i class="mr-1 fa-solid fa-star"></i> XP Reward
                            </label>
                            <input type="number" name="xp_reward" id="xp_reward"
                                value="{{ old('xp_reward', $challenge->xp_reward) }}" required min="1"
                                placeholder="Jumlah XP"
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('xp_reward') border-red-500 @enderror">
                            @error('xp_reward')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="mr-1 fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <!-- Tanggal Mulai -->
                        <div>
                            <label for="start_date" class="block mb-2 text-sm font-medium text-gray-700">
                                <i class="mr-1 fa-solid fa-calendar-plus"></i> Tanggal Mulai
                            </label>
                            <input type="date" name="start_date" id="start_date"
                                value="{{ old('start_date', $challenge->start_date->format('Y-m-d')) }}" required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('start_date') border-red-500 @enderror">
                            @error('start_date')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="mr-1 fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <!-- Tanggal Berakhir -->
                        <div>
                            <label for="end_date" class="block mb-2 text-sm font-medium text-gray-700">
                                <i class="mr-1 fa-solid fa-calendar-check"></i> Tanggal Berakhir
                            </label>
                            <input type="date" name="end_date" id="end_date"
                                value="{{ old('end_date', $challenge->end_date->format('Y-m-d')) }}" required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('end_date') border-red-500 @enderror">
                            @error('end_date')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="mr-1 fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="mb-2 font-medium text-gray-700">
                        <i class="mr-2 fa-solid fa-info-circle"></i> Informasi Tambahan
                    </h4>
                    <div class="grid grid-cols-2 gap-4 text-sm text-gray-600 md:grid-cols-3">
                        <div>
                            <span class="font-medium">ID Tantangan:</span> {{ $challenge->id }}
                        </div>
                        <div>
                            <span class="font-medium">Dibuat Oleh:</span> {{ $challenge->createdBy->name ?? 'System' }}
                        </div>
                        <div>
                            <span class="font-medium">Dibuat:</span> {{ $challenge->created_at->format('d M Y H:i') }}
                        </div>
                        <div>
                            <span class="font-medium">Partisipan:</span> {{ $challenge->participants_count ?? 0 }} orang
                        </div>
                        <div class="md:col-span-2">
                            <span class="font-medium">Terakhir Diupdate:</span> {{ $challenge->updated_at->format('d M Y
                            H:i') }}
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col-reverse gap-4 pt-6 border-t border-gray-200 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.challenges.index') }}"
                        class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-gray-700 transition-colors bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">
                        <i class="mr-2 fa-solid fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                    <button type="submit"
                        class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white transition-colors bg-yellow-600 rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <i class="mr-2 fa-solid fa-save"></i> Perbarui Tantangan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set minimum end date based on start date
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        startDateInput.addEventListener('change', function() {
            endDateInput.min = this.value;
            if (endDateInput.value < this.value) {
                endDateInput.value = this.value;
            }
        });

        // Initialize min date for end date
        endDateInput.min = startDateInput.value;
    });
</script>
@endpush
@endsection