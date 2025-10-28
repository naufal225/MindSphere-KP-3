@extends('components.admin.layout.app')

@section('header', 'Tambah User Baru')
@section('subtitle', 'Buat akun pengguna baru untuk sistem')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center space-x-3">
            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                <i class="text-xl text-blue-600 fa-solid fa-user-plus"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Tambah User Baru</h1>
                <p class="text-gray-600">Buat akun pengguna baru untuk sistem</p>
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
        <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Informasi Dasar -->
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <i class="mr-2 text-blue-500 fa-solid fa-id-card"></i>
                    <h3 class="text-lg font-semibold text-gray-800">Informasi Dasar</h3>
                </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Nama -->
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-1 fa-solid fa-signature"></i> Nama Lengkap
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                            placeholder="Masukkan nama lengkap">
                        @error('name')
                        <p class="mt-2 text-sm text-red-600"><i class="mr-1 fa-solid fa-circle-exclamation"></i> {{
                            $message }}</p>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div>
                        <label for="username" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-1 fa-solid fa-at"></i> Username
                        </label>
                        <input type="text" name="username" id="username" value="{{ old('username') }}"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('username') border-red-500 @enderror"
                            placeholder="mis. johndoe">
                        @error('username')
                        <p class="mt-2 text-sm text-red-600"><i class="mr-1 fa-solid fa-circle-exclamation"></i> {{
                            $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-1 fa-solid fa-envelope"></i> Alamat Email
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                            placeholder="email@example.com">
                        @error('email')
                        <p class="mt-2 text-sm text-red-600"><i class="mr-1 fa-solid fa-circle-exclamation"></i> {{
                            $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-1 fa-solid fa-lock"></i> Password
                        </label>
                        <input type="password" name="password" id="password"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                            placeholder="Minimal 6 karakter">
                        @error('password')
                        <p class="mt-2 text-sm text-red-600"><i class="mr-1 fa-solid fa-circle-exclamation"></i> {{
                            $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-1 fa-solid fa-user-tag"></i> Role Pengguna
                        </label>
                        <select name="role" id="role"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('role') border-red-500 @enderror">
                            <option value="">Pilih Role Pengguna</option>
                            <option value="guru" {{ old('role')==='guru' ? 'selected' : '' }}>
                                <i class="fa-solid fa-chalkboard-user"></i> Guru
                            </option>
                            <option value="siswa" {{ old('role')==='siswa' ? 'selected' : '' }}>
                                <i class="fa-solid fa-graduation-cap"></i> Siswa
                            </option>
                            <option value="ortu" {{ old('role')==='ortu' ? 'selected' : '' }}>
                                <i class="fa-solid fa-user-group"></i> Orang Tua
                            </option>
                        </select>
                        @error('role')
                        <p class="mt-2 text-sm text-red-600"><i class="mr-1 fa-solid fa-circle-exclamation"></i> {{
                            $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Parent Selection (Conditional for Siswa) -->
            <div class="mb-8" id="parent-section" style="display: none;">
                <div class="flex items-center mb-4">
                    <i class="mr-2 text-orange-500 fa-solid fa-user-group"></i>
                    <h3 class="text-lg font-semibold text-gray-800">Penunjukan Orang Tua</h3>
                </div>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="parent_id" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-1 fa-solid fa-users"></i> Pilih Orang Tua
                        </label>
                        <select name="parent_id" id="parent_id"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('parent_id') border-red-500 @enderror">
                            <option value="">Pilih Orang Tua (Opsional)</option>
                            @foreach($parents as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id')==$parent->id ? 'selected' : '' }}>
                                {{ $parent->name }} ({{ $parent->email }})
                            </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                        <p class="mt-2 text-sm text-red-600"><i class="mr-1 fa-solid fa-circle-exclamation"></i>
                            {{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="mr-1 fa-solid fa-info-circle"></i>
                            Pilih orang tua yang akan terhubung dengan siswa ini (opsional)
                        </p>
                    </div>
                </div>
            </div>

            <!-- Kelas (Conditional) -->
            <div class="mb-8" id="class-section" style="display: none;">
                <div class="flex items-center mb-4">
                    <i class="mr-2 text-indigo-500 fa-solid fa-school"></i>
                    <h3 class="text-lg font-semibold text-gray-800">Penugasan Kelas</h3>
                </div>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="class_id" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-1 fa-solid fa-building-columns"></i> Kelas
                        </label>
                        <select name="class_id" id="class_id"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('class_id') border-red-500 @enderror">
                            <option value="">Pilih Kelas</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id')==$class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('class_id')
                        <p class="mt-2 text-sm text-red-600"><i class="mr-1 fa-solid fa-circle-exclamation"></i> {{
                            $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="mr-1 fa-solid fa-info-circle"></i>
                            <span id="class-help-text">Pilih kelas yang sesuai dengan role pengguna.</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Informasi Tambahan -->
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <i class="mr-2 text-green-500 fa-solid fa-chart-line"></i>
                    <h3 class="text-lg font-semibold text-gray-800">Informasi Tambahan</h3>
                </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- XP -->
                    <div>
                        <label for="xp" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-1 fa-solid fa-star"></i> Experience Points (XP)
                        </label>
                        <input type="number" name="xp" id="xp" value="{{ old('xp', 0) }}" min="0"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('xp') border-red-500 @enderror">
                        @error('xp')
                        <p class="mt-2 text-sm text-red-600"><i class="mr-1 fa-solid fa-circle-exclamation"></i> {{
                            $message }}</p>
                        @enderror
                    </div>

                    <!-- Level -->
                    <div>
                        <label for="level" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-1 fa-solid fa-level-up-alt"></i> Level
                        </label>
                        <input type="number" name="level" id="level" value="{{ old('level', 1) }}" min="1" max="100"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('level') border-red-500 @enderror">
                        @error('level')
                        <p class="mt-2 text-sm text-red-600"><i class="mr-1 fa-solid fa-circle-exclamation"></i> {{
                            $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Avatar Section -->
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <i class="mr-2 text-purple-500 fa-solid fa-image"></i>
                    <h3 class="text-lg font-semibold text-gray-800">Foto Profil</h3>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Avatar Preview -->
                    <div class="lg:col-span-1">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Preview Avatar</label>
                        <div
                            class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-lg">
                            <div id="avatarPreview" class="mb-4">
                                <div
                                    class="flex items-center justify-center w-32 h-32 text-4xl font-bold text-white bg-gradient-to-r from-blue-500 to-purple-600 rounded-full shadow-lg">
                                    <span id="avatarInitial">{{ old('name') ? substr(old('name'), 0, 1) : 'U' }}</span>
                                </div>
                            </div>
                            <img id="avatarImagePreview" src="" alt="Preview"
                                class="hidden w-32 h-32 rounded-full shadow-lg">
                            <p class="text-sm text-center text-gray-500">Preview akan muncul di sini</p>
                        </div>
                    </div>

                    <!-- Avatar Upload Options -->
                    <div class="lg:col-span-2">
                        <div class="space-y-6">
                            <!-- Upload File -->
                            <div>
                                <label for="avatar_file" class="block mb-2 text-sm font-medium text-gray-700">
                                    <i class="mr-1 fa-solid fa-upload"></i> Upload Avatar dari Komputer
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="file" name="avatar_file" id="avatar_file" accept="image/*"
                                        class="flex-1 px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('avatar_file') border-red-500 @enderror">
                                </div>
                                @error('avatar_file')
                                <p class="mt-2 text-sm text-red-600"><i class="mr-1 fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-sm text-gray-500">
                                    <i class="mr-1 fa-solid fa-info-circle"></i> Format: JPEG, PNG, JPG. Maksimal 2MB.
                                </p>
                            </div>

                            <!-- Avatar URL -->
                            <div>
                                <label for="avatar_url" class="block mb-2 text-sm font-medium text-gray-700">
                                    <i class="mr-1 fa-solid fa-link"></i> Atau Gunakan URL Avatar
                                </label>
                                <input type="url" name="avatar_url" id="avatar_url" value="{{ old('avatar_url') }}"
                                    placeholder="https://example.com/avatar.jpg"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('avatar_url') border-red-500 @enderror">
                                @error('avatar_url')
                                <p class="mt-2 text-sm text-red-600"><i class="mr-1 fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col-reverse gap-4 pt-6 border-t border-gray-200 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-gray-700 transition-colors bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">
                    <i class="mr-2 fa-solid fa-arrow-left"></i> Kembali ke Daftar
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="mr-2 fa-solid fa-save"></i> Simpan User Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const classSection = document.getElementById('class-section');
        const parentSection = document.getElementById('parent-section');
        const classHelpText = document.getElementById('class-help-text');
        const classSelect = document.getElementById('class_id');
        const parentSelect = document.getElementById('parent_id');

        const nameInput = document.getElementById('name');
        const avatarFileInput = document.getElementById('avatar_file');
        const avatarUrlInput = document.getElementById('avatar_url');
        const avatarPreview = document.getElementById('avatarPreview');
        const avatarImagePreview = document.getElementById('avatarImagePreview');
        const avatarInitial = document.getElementById('avatarInitial');

        // Toggle sections berdasarkan role
        function toggleSections() {
            const role = roleSelect.value;

            // Toggle class section
            if (role === 'guru' || role === 'siswa') {
                classSection.style.display = 'block';
                if (role === 'siswa') {
                    classHelpText.textContent = 'Wajib dipilih untuk siswa.';
                    classSelect.setAttribute('required', 'required');
                } else if (role === 'guru') {
                    classHelpText.textContent = 'Opsional untuk guru (akan menjadi wali kelas).';
                    classSelect.removeAttribute('required');
                }
            } else {
                classSection.style.display = 'none';
                classSelect.removeAttribute('required');
                classSelect.value = '';
            }

            // Toggle parent section
            if (role === 'siswa') {
                parentSection.style.display = 'block';
            } else {
                parentSection.style.display = 'none';
                parentSelect.value = '';
            }
        }

        // Event listener untuk role change
        roleSelect.addEventListener('change', toggleSections);

        // Panggil pada load
        toggleSections();

        // Avatar logic
        nameInput.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                avatarInitial.textContent = this.value.trim().charAt(0).toUpperCase();
            } else {
                avatarInitial.textContent = 'U';
            }
        });

        avatarFileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('File terlalu besar! Maksimal 2MB.');
                    this.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarImagePreview.src = e.target.result;
                    avatarImagePreview.classList.remove('hidden');
                    avatarPreview.classList.add('hidden');
                    avatarUrlInput.value = '';
                };
                reader.readAsDataURL(file);
            } else {
                avatarImagePreview.classList.add('hidden');
                avatarPreview.classList.remove('hidden');
            }
        });

        avatarUrlInput.addEventListener('input', function() {
            const url = this.value.trim();
            if (url === '') {
                avatarImagePreview.classList.add('hidden');
                avatarPreview.classList.remove('hidden');
                return;
            }
            if (url.match(/\.(jpeg|jpg|gif|png|webp)$/i)) {
                avatarImagePreview.src = url;
                avatarImagePreview.onload = function() {
                    avatarImagePreview.classList.remove('hidden');
                    avatarPreview.classList.add('hidden');
                };
                avatarImagePreview.onerror = function() {
                    avatarImagePreview.classList.add('hidden');
                    avatarPreview.classList.remove('hidden');
                };
                avatarFileInput.value = '';
            }
        });

        // Show preview if there's existing URL value on page load
        if (avatarUrlInput.value.trim() !== '') {
            avatarImagePreview.src = avatarUrlInput.value;
            avatarImagePreview.classList.remove('hidden');
            avatarPreview.classList.add('hidden');
        }
    });
</script>
@endpush
