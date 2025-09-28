@extends('components.admin.layout.app')

@section('header', 'Edit User')
@section('subtitle', 'Perbarui informasi pengguna')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center space-x-3">
            <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-lg">
                <i class="text-xl text-yellow-600 fa-solid fa-user-edit"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Edit User</h1>
                <p class="text-gray-600">Perbarui informasi untuk {{ $user->name }}</p>
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
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

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
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                            placeholder="Masukkan nama lengkap">
                        @error('name')
                        <p class="mt-2 text-sm text-red-600"><i class="mr-1 fa-solid fa-circle-exclamation"></i> {{
                            $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-1 fa-solid fa-envelope"></i> Alamat Email
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
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
                            <i class="mr-1 fa-solid fa-lock"></i> Password Baru
                        </label>
                        <input type="password" name="password" id="password"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                            placeholder="Kosongkan jika tidak ingin mengubah">
                        @error('password')
                        <p class="mt-2 text-sm text-red-600"><i class="mr-1 fa-solid fa-circle-exclamation"></i> {{
                            $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">
                            <i class="mr-1 fa-solid fa-info-circle"></i> Biarkan kosong untuk mempertahankan password
                            saat ini
                        </p>
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-1 fa-solid fa-user-tag"></i> Role Pengguna
                        </label>
                        <select name="role" id="role"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('role') border-red-500 @enderror">
                            <option value="">Pilih Role Pengguna</option>
                            <option value="guru" {{ old('role', $user->role) === 'guru' ? 'selected' : '' }}>
                                <i class="fa-solid fa-chalkboard-user"></i> Guru
                            </option>
                            <option value="siswa" {{ old('role', $user->role) === 'siswa' ? 'selected' : '' }}>
                                <i class="fa-solid fa-graduation-cap"></i> Siswa
                            </option>
                            <option value="ortu" {{ old('role', $user->role) === 'ortu' ? 'selected' : '' }}>
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

            <!-- Penugasan Kelas (Conditional) -->
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
                            <option value="{{ $class->id }}" {{ (old('class_id') ?? ($user->role === 'guru' ?
                                $class->teacher_id : $user->classAsStudent->pluck('id')->first())) == $class->id ?
                                'selected' : '' }}>
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
                        <input type="number" name="xp" id="xp" value="{{ old('xp', $user->xp) }}" min="0"
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
                        <input type="number" name="level" id="level" value="{{ old('level', $user->level) }}" min="1"
                            max="100"
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
                            <!-- Current Avatar -->
                            <div id="currentAvatar" class="mb-4">
                                @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="Current Avatar"
                                    class="w-32 h-32 rounded-full shadow-lg" id="currentAvatarImage">
                                @else
                                <div
                                    class="flex items-center justify-center w-32 h-32 text-4xl font-bold text-white bg-gradient-to-r from-blue-500 to-purple-600 rounded-full shadow-lg">
                                    <span>{{ substr($user->name, 0, 1) }}</span>
                                </div>
                                @endif
                            </div>

                            <!-- New Avatar Preview -->
                            <div id="newAvatarPreview" class="hidden mb-4">
                                <img id="avatarImagePreview" src="" alt="New Avatar Preview"
                                    class="w-32 h-32 rounded-full shadow-lg">
                            </div>

                            <p class="text-sm text-center text-gray-500" id="previewText">
                                Avatar saat ini akan diganti
                            </p>
                        </div>
                    </div>

                    <!-- Avatar Upload Options -->
                    <div class="lg:col-span-2">
                        <div class="space-y-6">
                            <!-- Upload File -->
                            <div>
                                <label for="avatar_file" class="block mb-2 text-sm font-medium text-gray-700">
                                    <i class="mr-1 fa-solid fa-upload"></i> Upload Avatar Baru
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
                                    <i class="mr-1 fa-solid fa-link"></i> Atau Gunakan URL Avatar Baru
                                </label>
                                <input type="url" name="avatar_url" id="avatar_url"
                                    value="{{ old('avatar_url', $user->avatar_url) }}"
                                    placeholder="https://example.com/avatar.jpg"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('avatar_url') border-red-500 @enderror">
                                @error('avatar_url')
                                <p class="mt-2 text-sm text-red-600"><i class="mr-1 fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Remove Avatar Option -->
                            <div class="p-4 bg-red-50 rounded-lg">
                                <label class="flex items-center">
                                    <input type="checkbox" name="remove_avatar" id="remove_avatar" value="1"
                                        class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    <span class="ml-2 text-sm font-medium text-red-700">
                                        <i class="mr-1 fa-solid fa-trash"></i> Hapus avatar saat ini
                                    </span>
                                </label>
                                <p class="mt-1 text-xs text-red-600">
                                    Centang untuk menghapus avatar dan menggunakan avatar default
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Info Summary -->
            <div class="p-4 mb-6 bg-gray-50 rounded-lg">
                <h4 class="mb-2 font-medium text-gray-700"><i class="mr-2 fa-solid fa-info-circle"></i> Informasi User
                </h4>
                <div class="grid grid-cols-2 gap-4 text-sm text-gray-600 md:grid-cols-4">
                    <div>
                        <span class="font-medium">ID:</span> {{ $user->id }}
                    </div>
                    <div>
                        <span class="font-medium">Bergabung:</span> {{ $user->created_at ? $user->created_at->format('d
                        M Y') : '-' }}
                    </div>
                    <div>
                        <span class="font-medium">Diperbarui:</span> {{ $user->updated_at ? $user->updated_at->format('d
                        M Y') : '-' }}
                    </div>
                    <div>
                        <span class="font-medium">Status:</span>
                        <span
                            class="px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">Aktif</span>
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
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white transition-colors bg-yellow-600 rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <i class="mr-2 fa-solid fa-save"></i> Perbarui User
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
    const classHelpText = document.getElementById('class-help-text');
    const classSelect = document.getElementById('class_id');

    const nameInput = document.getElementById('name');
    const avatarFileInput = document.getElementById('avatar_file');
    const avatarUrlInput = document.getElementById('avatar_url');
    const removeAvatarCheckbox = document.getElementById('remove_avatar');
    const currentAvatar = document.getElementById('currentAvatar');
    const newAvatarPreview = document.getElementById('newAvatarPreview');
    const avatarImagePreview = document.getElementById('avatarImagePreview');
    const previewText = document.getElementById('previewText');
    const currentAvatarImage = document.getElementById('currentAvatarImage');

    // Toggle kelas berdasarkan role
    function toggleClassSection() {
        const role = roleSelect.value;
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
        }
    }

    roleSelect.addEventListener('change', toggleClassSection);
    toggleClassSection(); // on load

    // Handle file upload preview
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
                newAvatarPreview.classList.remove('hidden');
                currentAvatar.classList.add('hidden');
                previewText.textContent = 'Preview avatar baru';
                avatarUrlInput.value = '';
                removeAvatarCheckbox.checked = false;
            };
            reader.readAsDataURL(file);
        } else {
            newAvatarPreview.classList.add('hidden');
            currentAvatar.classList.remove('hidden');
            previewText.textContent = 'Avatar saat ini akan dipertahankan';
        }
    });

    // Handle URL input preview
    avatarUrlInput.addEventListener('input', function() {
        const url = this.value.trim();
        if (url === '') {
            newAvatarPreview.classList.add('hidden');
            currentAvatar.classList.remove('hidden');
            previewText.textContent = 'Avatar saat ini akan dipertahankan';
            return;
        }
        if (url.match(/\.(jpeg|jpg|gif|png|webp)$/i)) {
            avatarImagePreview.src = url;
            avatarImagePreview.onload = function() {
                newAvatarPreview.classList.remove('hidden');
                currentAvatar.classList.add('hidden');
                previewText.textContent = 'Preview avatar baru dari URL';
            };
            avatarImagePreview.onerror = function() {
                newAvatarPreview.classList.add('hidden');
                currentAvatar.classList.remove('hidden');
                previewText.textContent = 'Avatar saat ini akan dipertahankan';
            };
            avatarFileInput.value = '';
            removeAvatarCheckbox.checked = false;
        }
    });

    // Handle remove avatar checkbox
    removeAvatarCheckbox.addEventListener('change', function() {
        if (this.checked) {
            newAvatarPreview.classList.add('hidden');
            currentAvatar.classList.remove('hidden');
            if (currentAvatarImage) {
                currentAvatarImage.classList.add('hidden');
            }
            previewText.textContent = 'Avatar akan dihapus dan menggunakan default';
            avatarFileInput.value = '';
            avatarUrlInput.value = '';
        } else {
            if (currentAvatarImage) {
                currentAvatarImage.classList.remove('hidden');
            }
            previewText.textContent = 'Avatar saat ini akan dipertahankan';
        }
    });

    // Show preview if there's existing URL value on page load
    if (avatarUrlInput.value.trim() !== '' && avatarUrlInput.value !== '{{ $user->avatar_url }}') {
        avatarImagePreview.src = avatarUrlInput.value;
        newAvatarPreview.classList.remove('hidden');
        currentAvatar.classList.add('hidden');
        previewText.textContent = 'Preview avatar baru dari URL';
    }
});
</script>
@endpush