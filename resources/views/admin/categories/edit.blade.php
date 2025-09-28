@extends('components.admin.layout.app')

@section('header', 'Edit Kategori')
@section('subtitle', 'Perbarui data kategori')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center space-x-3">
            <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-lg">
                <i class="text-xl text-yellow-600 fa-solid fa-tags"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Edit Kategori</h1>
                <p class="text-gray-600">Perbarui informasi untuk kategori {{ $category->name }}</p>
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
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Informasi Kategori -->
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <i class="mr-2 text-blue-500 fa-solid fa-tag"></i>
                    <h3 class="text-lg font-semibold text-gray-800">Informasi Kategori</h3>
                </div>
                <div class="grid grid-cols-1 gap-6">
                    <!-- Code Field -->
                    <div>
                        <label for="code" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-1 fa-solid fa-code"></i> Kode Kategori
                        </label>
                        <select name="code" id="code" required
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('code') border-red-500 @enderror">
                            <option value="">Pilih Kode Kategori</option>
                            @foreach(\App\Enums\CategoryCode::cases() as $code)
                            <option value="{{ $code->value }}" {{ (old('code') ?? $category->code->value) ==
                                $code->value ? 'selected' : '' }}>
                                {{ $code->value }} - {{ $code->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('code')
                        <p class="mt-2 text-sm text-red-600">
                            <i class="mr-1 fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-1 fa-solid fa-heading"></i> Nama Kategori
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
                            placeholder="Masukkan nama kategori"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                        @error('name')
                        <p class="mt-2 text-sm text-red-600">
                            <i class="mr-1 fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <!-- Description Field -->
                    <div>
                        <label for="description" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-1 fa-solid fa-align-left"></i> Deskripsi
                        </label>
                        <textarea name="description" id="description" rows="4" required
                            placeholder="Masukkan deskripsi kategori..."
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                        <p class="mt-2 text-sm text-red-600">
                            <i class="mr-1 fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Informasi Tambahan -->
            <div class="p-4 mb-6 bg-gray-50 rounded-lg">
                <h4 class="mb-2 font-medium text-gray-700">
                    <i class="mr-2 fa-solid fa-info-circle"></i> Informasi Tambahan
                </h4>
                <div class="grid grid-cols-2 gap-4 text-sm text-gray-600 md:grid-cols-3">
                    <div>
                        <span class="font-medium">ID Kategori:</span> {{ $category->id }}
                    </div>
                    <div>
                        <span class="font-medium">Dibuat:</span> {{ $category->created_at->format('d M Y H:i') }}
                    </div>
                    <div>
                        <span class="font-medium">Terakhir Diupdate:</span> {{ $category->updated_at->format('d M Y
                        H:i') }}
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col-reverse gap-4 pt-6 border-t border-gray-200 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.categories.index') }}"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-gray-700 transition-colors bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">
                    <i class="mr-2 fa-solid fa-arrow-left"></i> Kembali ke Daftar
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white transition-colors bg-yellow-600 rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <i class="mr-2 fa-solid fa-save"></i> Perbarui Kategori
                </button>
            </div>
        </form>
    </div>
</div>
@endsection