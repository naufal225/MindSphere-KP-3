@extends('components.admin.layout.app')

@section('header', 'Buat Kebiasaan Baru')
@section('subtitle', 'Buat kebiasaan baru untuk pengguna')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center space-x-3">
            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                <i class="text-xl text-blue-600 fa-solid fa-repeat"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Buat Kebiasaan Baru</h1>
                <p class="text-gray-600">Isi form berikut untuk membuat kebiasaan baru</p>
            </div>
        </div>
    </div>

    <div class="p-6 bg-white rounded-lg shadow-sm border border-gray-100">
        <form action="{{ route('admin.habits.store') }}" method="POST">
            @csrf

            <div class="space-y-8">
                <!-- Informasi Dasar -->
                <div>
                    <div class="flex items-center mb-4">
                        <i class="mr-2 text-blue-500 fa-solid fa-info-circle"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Informasi Dasar</h3>
                    </div>
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Judul Kebiasaan -->
                        <div>
                            <label for="title" class="block mb-2 text-sm font-medium text-gray-700">
                                <i class="mr-1 fa-solid fa-heading"></i> Judul Kebiasaan
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                placeholder="Contoh: Olahraga Pagi, Membaca Buku, dll."
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror">
                            @error('title')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="mr-1 fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label for="description" class="block mb-2 text-sm font-medium text-gray-700">
                                <i class="mr-1 fa-solid fa-align-left"></i> Deskripsi Kebiasaan
                            </label>
                            <textarea name="description" id="description" rows="4" required
                                placeholder="Jelaskan detail kebiasaan, manfaat, dan cara melakukannya..."
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="mr-1 fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Kategori -->
                <div>
                    <div class="flex items-center mb-4">
                        <i class="mr-2 text-green-500 fa-solid fa-tags"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Kategori</h3>
                    </div>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Kategori -->
                        <div>
                            <label for="category_id" class="block mb-2 text-sm font-medium text-gray-700">
                                <i class="mr-1 fa-solid fa-folder"></i> Kategori
                            </label>
                            <select name="category_id" id="category_id" required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('category_id') border-red-500 @enderror">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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

                        {{-- <!-- Tipe Kebiasaan -->
                        <div>
                            <label for="type" class="block mb-2 text-sm font-medium text-gray-700">
                                <i class="mr-1 fa-solid fa-user"></i> Tipe Kebiasaan
                            </label>
                            <select name="type" id="type" required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-500 @enderror">
                                <option value="">Pilih Tipe</option>
                                <option value="self" {{ old('type') == 'self' ? 'selected' : '' }}>Mandiri</option>
                                <option value="assigned" {{ old('type') == 'assigned' ? 'selected' : '' }}>Ditugaskan</option>
                            </select>
                            @error('type')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="mr-1 fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                        </div> --}}
                    </div>
                </div>

                <!-- Pengaturan Tambahan -->
                <div>
                    <div class="flex items-center mb-4">
                        <i class="mr-2 text-yellow-500 fa-solid fa-cog"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Pengaturan Tambahan</h3>
                    </div>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Periode -->
                        <div>
                            <label for="period" class="block mb-2 text-sm font-medium text-gray-700">
                                <i class="mr-1 fa-solid fa-calendar"></i> Periode Kebiasaan
                            </label>
                            <select name="period" id="period" required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('period') border-red-500 @enderror">
                                <option value="">Pilih Periode</option>
                                <option value="daily" {{ old('period') == 'daily' ? 'selected' : '' }}>Harian</option>
                                <option value="weekly" {{ old('period') == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                            </select>
                            @error('period')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="mr-1 fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <!-- Ditugaskan Oleh (Conditional) -->
                        <div id="assigned_by_field" class="hidden">
                            <label for="assigned_by" class="block mb-2 text-sm font-medium text-gray-700">
                                <i class="mr-1 fa-solid fa-user-check"></i> Ditugaskan Oleh
                            </label>
                            <select name="assigned_by" id="assigned_by"
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('assigned_by') border-red-500 @enderror">
                                <option value="">Pilih Pengguna</option>
                                <!-- Options akan diisi via JavaScript atau dari controller -->
                            </select>
                            @error('assigned_by')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="mr-1 fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Pilih guru/admin yang menugaskan kebiasaan ini</p>
                        </div>

                        <!-- Placeholder untuk menjaga layout -->
                        <div id="placeholder_field"></div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col-reverse gap-4 pt-6 border-t border-gray-200 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.habits.index') }}"
                        class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-gray-700 transition-colors bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">
                        <i class="mr-2 fa-solid fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                    <button type="submit"
                        class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="mr-2 fa-solid fa-plus"></i> Buat Kebiasaan
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
                <h3 class="font-semibold text-blue-800">Panduan Membuat Kebiasaan</h3>
                <ul class="mt-2 text-sm text-blue-700 list-disc list-inside space-y-1">
                    <li><strong>Kebiasaan Mandiri</strong>: Dapat dipilih dan dilakukan secara sukarela oleh pengguna</li>
                    <li><strong>Kebiasaan Ditugaskan</strong>: Harus dilakukan oleh pengguna tertentu yang ditugaskan</li>
                    <li><strong>Harian</strong>: Kebiasaan yang dilakukan setiap hari</li>
                    <li><strong>Mingguan</strong>: Kebiasaan yang dilakukan sekali dalam seminggu</li>
                    <li>Pilih kategori yang sesuai untuk memudahkan pengelompokan</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Type Description Cards -->
    <div class="grid grid-cols-1 gap-4 mt-6 md:grid-cols-2">
        <!-- Self Habit Card -->
        <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center mb-2">
                <i class="mr-2 text-green-600 fa-solid fa-user"></i>
                <h4 class="font-semibold text-green-800">Kebiasaan Mandiri</h4>
            </div>
            <p class="text-sm text-green-700">
                Kebiasaan yang dapat dipilih secara sukarela oleh semua pengguna. Cocok untuk kebiasaan umum
                seperti olahraga, membaca, atau meditasi yang bermanfaat untuk semua orang.
            </p>
        </div>

        <!-- Assigned Habit Card -->
        <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg">
            <div class="flex items-center mb-2">
                <i class="mr-2 text-purple-600 fa-solid fa-user-check"></i>
                <h4 class="font-semibold text-purple-800">Kebiasaan Ditugaskan</h4>
            </div>
            <p class="text-sm text-purple-700">
                Kebiasaan yang ditugaskan kepada pengguna tertentu oleh guru atau admin. Cocok untuk tugas
                khusus, pekerjaan rumah, atau aktivitas yang wajib dilakukan.
            </p>
        </div>
    </div>
</div>
{{--
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const assignedByField = document.getElementById('assigned_by_field');
        const placeholderField = document.getElementById('placeholder_field');
        const assignedBySelect = document.getElementById('assigned_by');

        // Function to toggle assigned_by field
        function toggleAssignedByField() {
            if (typeSelect.value === 'assigned') {
                assignedByField.classList.remove('hidden');
                assignedByField.classList.add('block');
                placeholderField.classList.add('hidden');
                // Make assigned_by required
                assignedBySelect.required = true;
            } else {
                assignedByField.classList.remove('block');
                assignedByField.classList.add('hidden');
                placeholderField.classList.remove('hidden');
                placeholderField.classList.add('block');
                // Remove required attribute
                assignedBySelect.required = false;
                // Clear value
                assignedBySelect.value = '';
            }
        }

        // Initial toggle
        toggleAssignedByField();

        // Add event listener for type change
        typeSelect.addEventListener('change', toggleAssignedByField);

        // Load users for assigned_by select (you can fetch this from API or preload in controller)
        function loadUsers() {
            // This is a placeholder - you might want to fetch users via AJAX
            // or preload them in the controller and pass to view
            const users = {!! json_encode($users ?? []) !!};

            if (users.length > 0) {
                assignedBySelect.innerHTML = '<option value="">Pilih Pengguna</option>';
                users.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = user.name + ' (' + user.email + ')';
                    assignedBySelect.appendChild(option);
                });

                // Set old value if exists
                const oldValue = '{{ old("assigned_by") }}';
                if (oldValue) {
                    assignedBySelect.value = oldValue;
                }
            }
        }

        // Load users if assigned_by field is visible
        if (typeSelect.value === 'assigned') {
            loadUsers();
        }
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const type = document.getElementById('type').value;
        const assignedBy = document.getElementById('assigned_by').value;

        if (type === 'assigned' && !assignedBy) {
            e.preventDefault();
            alert('Harap pilih pengguna yang menugaskan untuk kebiasaan bertipe "Ditugaskan"');
            document.getElementById('assigned_by').focus();
        }
    });
</script>
@endpush --}}
@endsection
