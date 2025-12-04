@extends('components.admin.layout.app')

@section('header', 'Buat Tantangan Baru')
@section('subtitle', 'Buat tantangan baru untuk pengguna')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center space-x-3">
            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                <i class="text-xl text-blue-600 fa-solid fa-flag"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Buat Tantangan Baru</h1>
                <p class="text-gray-600">Isi form berikut untuk membuat tantangan baru</p>
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
        <form action="{{ route('admin.challenges.store') }}" method="POST">
            @csrf

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
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                placeholder="Masukkan judul tantangan yang menarik"
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
                                <option value="{{ $category->id }}" {{ old('category_id')==$category->id ? 'selected' :
                                    '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('category_id')
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
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
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
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                        <!-- XP Reward -->
                        <div>
                            <label for="xp_reward" class="block mb-2 text-sm font-medium text-gray-700">
                                <i class="mr-1 fa-solid fa-star"></i> XP Reward
                            </label>
                            <input type="number" name="xp_reward" id="xp_reward" value="{{ old('xp_reward', 100) }}"
                                required min="1" placeholder="Jumlah XP"
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('xp_reward') border-red-500 @enderror">
                            @error('xp_reward')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="mr-1 fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Pengalaman poin untuk pengguna</p>
                        </div>

                        <!-- Coin Reward -->
                        <div>
                            <label for="coin_reward" class="block mb-2 text-sm font-medium text-gray-700">
                                <i class="mr-1 fa-solid fa-coins"></i> Coin Reward
                            </label>
                            <input type="number" name="coin_reward" id="coin_reward"
                                value="{{ old('coin_reward', 50) }}" required min="0" placeholder="Jumlah Koin"
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('coin_reward') border-red-500 @enderror">
                            @error('coin_reward')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="mr-1 fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Koin untuk ditukarkan hadiah</p>
                        </div>

                        <!-- Tanggal Mulai -->
                        <div>
                            <label for="start_date" class="block mb-2 text-sm font-medium text-gray-700">
                                <i class="mr-1 fa-solid fa-calendar-plus"></i> Tanggal Mulai
                            </label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                                required
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
                            <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('end_date') border-red-500 @enderror">
                            @error('end_date')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="mr-1 fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
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
                        class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="mr-2 fa-solid fa-plus"></i> Buat Tantangan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Info Card -->
    <div class="p-6 mt-6 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start">
            <i class="mt-1 mr-3 text-blue-500 fa-solid fa-circle-info"></i>
            <div>
                <h3 class="font-semibold text-blue-800">Tips Membuat Tantangan yang Baik</h3>
                <ul class="mt-2 text-sm text-blue-700 list-disc list-inside space-y-1">
                    <li>Buat judul yang menarik dan jelas</li>
                    <li>Deskripsi harus detail dan mudah dipahami</li>
                    <li>Atur periode waktu yang realistis</li>
                    <li>Berikan XP dan Coin reward yang sesuai dengan tingkat kesulitan</li>
                    <li>Pilih kategori yang relevan dengan konten tantangan</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set minimum end date based on start date
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        // Set default dates
        const today = new Date().toISOString().split('T')[0];
        const nextWeek = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

        if (!startDateInput.value) {
            startDateInput.value = today;
        }
        if (!endDateInput.value) {
            endDateInput.value = nextWeek;
        }

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