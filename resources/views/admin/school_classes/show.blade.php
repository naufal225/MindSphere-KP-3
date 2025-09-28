@extends('components.admin.layout.app')

@section('header', 'Detail Kelas')
@section('subtitle', 'Informasi lengkap tentang kelas')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <div class="flex items-center space-x-3">
            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                <i class="text-xl text-blue-600 fa-solid fa-school-circle-check"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Kelas</h1>
                <p class="text-gray-600">Informasi lengkap tentang {{ $class->name }}</p>
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

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Kelas Info Card -->
        <div class="lg:col-span-1">
            <div class="overflow-hidden bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="p-6 text-center bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="text-4xl font-bold text-gray-800">{{ $class->name }}</div>
                    <p class="mt-2 text-gray-600">Kelas Sekolah</p>

                    @if($class->teacher)
                    <div class="mt-4 p-3 bg-purple-100 rounded-lg">
                        <p class="text-sm font-medium text-purple-800">Wali Kelas</p>
                        <p class="font-semibold">{{ $class->teacher->name }}</p>
                    </div>
                    @else
                    <div class="mt-4 p-3 bg-gray-100 rounded-lg">
                        <p class="text-sm font-medium text-gray-600">Wali Kelas belum ditentukan</p>
                    </div>
                    @endif
                </div>

                <div class="p-6">
                    <div class="space-y-4">
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-2">
                                <i class="mr-2 text-green-500 fa-solid fa-users"></i>
                                <span class="text-2xl font-bold text-gray-800">{{ $class->students->count() }}</span>
                            </div>
                            <p class="text-sm text-gray-600">Jumlah Siswa</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students & Actions -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="mr-2 fa-solid fa-users"></i> Daftar Siswa
                    </h3>
                </div>
                <div class="p-6">
                    @if($class->students->count() > 0)
                    <div class="space-y-3">
                        @foreach($class->students as $student)
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div
                                class="flex items-center justify-center w-10 h-10 text-white bg-gradient-to-r from-green-500 to-teal-600 rounded-full">
                                {{ substr($student->name, 0, 1) }}
                            </div>
                            <div class="ml-4">
                                <p class="font-medium text-gray-900">{{ $student->name }}</p>
                                <p class="text-sm text-gray-600">{{ $student->email }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-6 text-gray-500">
                        <i class="mb-2 text-3xl fa-solid fa-user-group"></i>
                        <p>Tidak ada siswa di kelas ini</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col gap-3 mt-6 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.school_classes.index') }}"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-gray-700 transition-colors bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">
                    <i class="mr-2 fa-solid fa-arrow-left"></i> Kembali ke Daftar
                </a>
                <a href="{{ route('admin.school_classes.edit', $class->id) }}"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white transition-colors bg-yellow-600 rounded-lg hover:bg-yellow-700">
                    <i class="mr-2 fa-solid fa-edit"></i> Edit Kelas
                </a>
                <form action="{{ route('admin.school_classes.destroy', $class->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white transition-colors bg-red-600 rounded-lg hover:bg-red-700"
                        onclick="return confirm('Yakin ingin menghapus kelas {{ $class->name }}? Semua data terkait akan tetap ada, tapi kelas ini dihapus.')">
                        <i class="mr-2 fa-solid fa-trash"></i> Hapus Kelas
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection