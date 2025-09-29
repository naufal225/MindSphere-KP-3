@extends('components.admin.layout.app')

@section('header', 'Edit Badge')
@section('subtitle', 'Perbarui informasi badge')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center space-x-3">
            <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-lg">
                <i class="text-xl text-yellow-600 fa-solid fa-medal"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Edit Badge</h1>
                <p class="text-gray-600">Perbarui informasi untuk <strong>{{ $badge->name }}</strong></p>
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
        <form method="POST" action="{{ route('admin.badges.update', $badge->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Informasi Dasar -->
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <i class="mr-2 text-blue-500 fa-solid fa-id-card"></i>
                    <h3 class="text-lg font-semibold text-gray-800">Informasi Dasar</h3>
                </div>
                <div class="grid grid-cols-1 gap-6">
                    <!-- Nama Badge -->
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-1 fa-solid fa-signature"></i> Nama Badge
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $badge->name) }}"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                            placeholder="Contoh: Penjelajah Buku, Juara Olahraga, dll.">
                        @error('name')
                        <p class="mt-2 text-sm text-red-600"><i class="mr-1 fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label for="description" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-1 fa-solid fa-align-left"></i> Deskripsi Badge
                        </label>
                        <textarea name="description" id="description" rows="4"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                            placeholder="Jelaskan kriteria atau makna badge ini...">{{ old('description', $badge->description) }}</textarea>
                        @error('description')
                        <p class="mt-2 text-sm text-red-600"><i class="mr-1 fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Kategori & XP -->
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <i class="mr-2 text-green-500 fa-solid fa-tags"></i>
                    <h3 class="text-lg font-semibold text-gray-800">Kategori & Syarat</h3>
                </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Kategori -->
                    <div>
                        <label for="category_id" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-1 fa-solid fa-folder"></i> Kategori Badge
                        </label>
                        <select name="category_id" id="category_id"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('category_id') border-red-500 @enderror">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (old('category_id') ?? $badge->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <p class="mt-2 text-sm text-red-600"><i class="mr-1 fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- XP Required -->
                    <div>
                        <label for="xp_required" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-1 fa-solid fa-star"></i> XP yang Dibutuhkan (Opsional)
                        </label>
                        <input type="number" name="xp_required" id="xp_required" value="{{ old('xp_required', $badge->xp_required) }}" min="1"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('xp_required') border-red-500 @enderror"
                            placeholder="Contoh: 1000">
                        @error('xp_required')
                        <p class="mt-2 text-sm text-red-600"><i class="mr-1 fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="mr-1 fa-solid fa-info-circle"></i> Biarkan kosong jika tidak ada syarat XP.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Ikon Badge -->
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <i class="mr-2 text-purple-500 fa-solid fa-image"></i>
                    <h3 class="text-lg font-semibold text-gray-800">Ikon Badge</h3>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Preview Ikon -->
                    <div class="lg:col-span-1">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Preview Ikon</label>
                        <div class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-lg">
                            <!-- Current Icon -->
                            <div id="currentIcon" class="mb-4">
                                @if($badge->icon_url)
                                <img src="{{ asset('storage/' . $badge->icon_url) }}" alt="Current Icon"
                                    class="w-32 h-32 object-cover rounded-full shadow-lg border-2 border-white" id="currentIconImage">
                                @else
                                <div class="flex items-center justify-center w-32 h-32 text-4xl font-bold text-white bg-gradient-to-r from-blue-500 to-purple-600 rounded-full shadow-lg">
                                    <i class="fa-solid fa-medal"></i>
                                </div>
                                @endif
                            </div>

                            <!-- New Icon Preview -->
                            <div id="newIconPreview" class="hidden mb-4">
                                <img id="iconImagePreview" src="" alt="New Icon Preview"
                                    class="w-32 h-32 object-cover rounded-full shadow-lg border-2 border-white">
                            </div>

                            <p class="text-sm text-center text-gray-500" id="previewText">
                                Ikon saat ini akan dipertahankan
                            </p>
                        </div>
                    </div>

                    <!-- Upload Ikon -->
                    <div class="lg:col-span-2">
                        <div class="space-y-6">
                            <div>
                                <label for="icon" class="block mb-2 text-sm font-medium text-gray-700">
                                    <i class="mr-1 fa-solid fa-upload"></i> Ganti Ikon Badge
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="file" name="icon" id="icon" accept="image/*"
                                        class="flex-1 px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('icon') border-red-500 @enderror">
                                </div>
                                @error('icon')
                                <p class="mt-2 text-sm text-red-600"><i class="mr-1 fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-sm text-gray-500">
                                    <i class="mr-1 fa-solid fa-info-circle"></i> Format: JPEG, PNG, JPG, GIF, SVG. Maksimal 2MB.
                                </p>
                            </div>

                            <!-- Hapus Ikon -->
                            <div class="p-4 bg-red-50 rounded-lg">
                                <label class="flex items-center">
                                    <input type="checkbox" name="remove_icon" id="remove_icon" value="1"
                                        class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    <span class="ml-2 text-sm font-medium text-red-700">
                                        <i class="mr-1 fa-solid fa-trash"></i> Hapus ikon saat ini
                                    </span>
                                </label>
                                <p class="mt-1 text-xs text-red-600">
                                    Centang untuk menghapus ikon dan menggunakan ikon default
                                </p>
                            </div>

                            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-start">
                                    <i class="mt-1 mr-2 text-blue-600 fa-solid fa-lightbulb"></i>
                                    <div>
                                        <h4 class="font-medium text-blue-800">Tips Desain Ikon</h4>
                                        <ul class="mt-1 text-sm text-blue-700 list-disc list-inside space-y-1">
                                            <li>Gunakan latar transparan (PNG/SVG) untuk tampilan terbaik</li>
                                            <li>Resolusi minimal 200x200 piksel</li>
                                            <li>Hindari teks kecil yang sulit dibaca</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Badge Info Summary -->
            <div class="p-4 mb-6 bg-gray-50 rounded-lg">
                <h4 class="mb-2 font-medium text-gray-700"><i class="mr-2 fa-solid fa-info-circle"></i> Informasi Badge</h4>
                <div class="grid grid-cols-2 gap-4 text-sm text-gray-600 md:grid-cols-4">
                    <div>
                        <span class="font-medium">ID:</span> {{ $badge->id }}
                    </div>
                    <div>
                        <span class="font-medium">Pengguna:</span> {{ $badge->users_count ?? 0 }}
                    </div>
                    <div>
                        <span class="font-medium">Dibuat:</span> {{ $badge->created_at ? $badge->created_at->format('d M Y') : '-' }}
                    </div>
                    <div>
                        <span class="font-medium">Diperbarui:</span> {{ $badge->updated_at ? $badge->updated_at->format('d M Y') : '-' }}
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col-reverse gap-4 pt-6 border-t border-gray-200 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.badges.index') }}"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-gray-700 transition-colors bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">
                    <i class="mr-2 fa-solid fa-arrow-left"></i> Kembali ke Daftar
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white transition-colors bg-yellow-600 rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <i class="mr-2 fa-solid fa-save"></i> Perbarui Badge
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const iconInput = document.getElementById('icon');
        const removeIconCheckbox = document.getElementById('remove_icon');
        const currentIcon = document.getElementById('currentIcon');
        const newIconPreview = document.getElementById('newIconPreview');
        const iconImagePreview = document.getElementById('iconImagePreview');
        const previewText = document.getElementById('previewText');
        const currentIconImage = document.getElementById('currentIconImage');

        // Handle file upload preview
        iconInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('File terlalu besar! Maksimal 2MB.');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    iconImagePreview.src = e.target.result;
                    newIconPreview.classList.remove('hidden');
                    currentIcon.classList.add('hidden');
                    previewText.textContent = 'Preview ikon baru';
                    removeIconCheckbox.checked = false;
                };
                reader.readAsDataURL(file);
            } else {
                newIconPreview.classList.add('hidden');
                currentIcon.classList.remove('hidden');
                previewText.textContent = 'Ikon saat ini akan dipertahankan';
            }
        });

        // Handle remove icon checkbox
        removeIconCheckbox.addEventListener('change', function() {
            if (this.checked) {
                newIconPreview.classList.add('hidden');
                currentIcon.classList.remove('hidden');
                if (currentIconImage) {
                    currentIconImage.classList.add('hidden');
                }
                previewText.textContent = 'Ikon akan dihapus dan menggunakan default';
                iconInput.value = '';
            } else {
                if (currentIconImage) {
                    currentIconImage.classList.remove('hidden');
                }
                previewText.textContent = 'Ikon saat ini akan dipertahankan';
            }
        });
    });
</script>
@endpush
