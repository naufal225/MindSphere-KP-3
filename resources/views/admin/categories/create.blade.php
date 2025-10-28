@extends('components.admin.layout.app')

@section('header', 'Tambah Kategori')
@section('subtitle', 'Buat kategori konten baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="p-6 bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Form Tambah Kategori</h2>
            <p class="text-gray-600">Isi form berikut untuk menambahkan kategori baru</p>
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

        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                

                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        placeholder="Masukkan nama kategori"
                        class="block w-full px-4 py-3 mt-1 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Nama kategori yang akan ditampilkan</p>
                </div>

                <!-- Description Field -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">
                        Deskripsi <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" id="description" rows="4" required
                        placeholder="Masukkan deskripsi kategori..."
                        class="block w-full px-4 py-3 mt-1 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Penjelasan detail tentang kategori ini</p>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end pt-6 space-x-4 border-t border-gray-200">
                    <a href="{{ route('admin.categories.index') }}"
                        class="px-6 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2 text-sm font-medium text-white transition-colors bg-blue-600 border border-transparent rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="mr-2 fa-solid fa-save"></i> Simpan Kategori
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
                <h3 class="font-semibold text-blue-800">Informasi Kategori</h3>
                <p class="mt-1 text-sm text-blue-700">
                    Kategori digunakan untuk mengelompokkan konten seperti habits, challenges, badges, dan reflections.
                    Pastikan kode kategori unik dan deskripsi jelas untuk memudahkan pengguna.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
