@extends('components.admin.layout.app')

@section('header', 'Edit Kelas')
@section('subtitle', 'Perbarui informasi kelas')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center space-x-3">
            <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-lg">
                <i class="text-xl text-yellow-600 fa-solid fa-school"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Edit Kelas</h1>
                <p class="text-gray-600">Perbarui informasi untuk kelas {{ $class->name }}</p>
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
        <form method="POST" action="{{ route('admin.school_classes.update', $class->id) }}">
            @csrf
            @method('PUT')

            <!-- Informasi Kelas -->
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <i class="mr-2 text-blue-500 fa-solid fa-tag"></i>
                    <h3 class="text-lg font-semibold text-gray-800">Informasi Kelas</h3>
                </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Nama Kelas -->
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-1 fa-solid fa-signature"></i> Nama Kelas
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $class->name) }}"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                            placeholder="Contoh: X RPL 1">
                        @error('name')
                        <p class="mt-2 text-sm text-red-600"><i class="mr-1 fa-solid fa-circle-exclamation"></i> {{
                            $message }}</p>
                        @enderror
                    </div>

                    <!-- Wali Kelas -->
                    <div>
                        <label for="teacher_id" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-1 fa-solid fa-chalkboard-user"></i> Wali Kelas (Opsional)
                        </label>
                        <select name="teacher_id" id="teacher_id"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('teacher_id') border-red-500 @enderror">
                            <option value="">Pilih Guru</option>
                            @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ (old('teacher_id') ?? $class->teacher_id) ==
                                $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('teacher_id')
                        <p class="mt-2 text-sm text-red-600"><i class="mr-1 fa-solid fa-circle-exclamation"></i> {{
                            $message }}</p>
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
                        <span class="font-medium">ID Kelas:</span> {{ $class->id }}
                    </div>
                    <div>
                        <span class="font-medium">Dibuat:</span> {{ $class->created_at ? $class->created_at->format('d M
                        Y') : '-' }}
                    </div>
                    <div>
                        <span class="font-medium">Terakhir Diupdate:</span> {{ $class->updated_at ?
                        $class->updated_at->format('d M Y') : '-' }}
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col-reverse gap-4 pt-6 border-t border-gray-200 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.school_classes.index') }}"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-gray-700 transition-colors bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">
                    <i class="mr-2 fa-solid fa-arrow-left"></i> Kembali ke Daftar
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white transition-colors bg-yellow-600 rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <i class="mr-2 fa-solid fa-save"></i> Perbarui Kelas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection