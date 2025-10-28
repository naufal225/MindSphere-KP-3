@extends('components.admin.layout.app')

@section('title', 'Profil Admin - MindSphere')

@section('content')
<div class="space-y-6">
    <!-- Error & Success Messages -->
    @if(session('error'))
    <div class="p-4 border border-red-300 rounded-lg bg-red-50">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="text-red-400 fas fa-exclamation-circle"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                    Terjadi Kesalahan
                </h3>
                <div class="mt-2 text-sm text-red-700">
                    <p>{{ session('error') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(session('success'))
    <div class="p-4 border border-green-300 rounded-lg bg-green-50">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="text-green-400 fas fa-check-circle"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-green-800">
                    Sukses
                </h3>
                <div class="mt-2 text-sm text-green-700">
                    <p>{{ session('success') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Page Header -->
    <div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Profil Admin</h1>
            <p class="text-gray-600">Kelola informasi profil dan akun Anda</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Left Column - Avatar & Basic Info -->
        <div class="space-y-6">
            <!-- Avatar Card -->
            <div class="p-6 bg-white rounded-xl shadow-soft">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Foto Profil</h3>

                <div class="flex flex-col items-center space-y-4">
                    <!-- Avatar Display -->
                    <div class="relative flex items-center justify-center">
                        @php
                            $parts = preg_split('/\s+/', trim($user->name ?? ''));
                            $initials = strtoupper((mb_substr($parts[0] ?? '', 0, 1)) . (mb_substr($parts[1] ?? '', 0, 1)));
                        @endphp
                        @if($user->avatar_url)
                            <img id="avatar-preview" src="{{ Storage::url($user->avatar_url) }}" alt="Avatar"
                                 class="w-32 h-32 rounded-full border-4 border-gray-200 object-cover">
                        @else
                            <div id="avatar-initials"
                                 class="flex items-center justify-center w-32 h-32 rounded-full border-4 border-gray-200 bg-blue-100 text-blue-700 text-3xl font-semibold">
                                {{ $initials ?: 'U' }}
                            </div>
                            <img id="avatar-preview" src="" alt="Avatar"
                                 class="hidden w-32 h-32 rounded-full border-4 border-gray-200 object-cover">
                        @endif
                    </div>

                    <!-- Upload Form -->
                    <form id="avatar-form" action="{{ route('admin.profile.update-avatar') }}" method="POST"
                        enctype="multipart/form-data" class="w-full">
                        @csrf
                        <div class="space-y-3">
                            <input type="file" id="avatar-input" name="avatar" accept="image/*" class="hidden"
                                onchange="previewAvatar(this)">

                            <button type="button" onclick="document.getElementById('avatar-input').click()"
                                class="w-full px-4 py-2 text-sm font-medium text-blue-600 bg-blue-100 border border-blue-300 rounded-lg hover:bg-blue-200">
                                <i class="mr-2 fas fa-camera"></i>Ubah Foto
                            </button>

                            <p class="text-xs text-center text-gray-500">
                                Format: JPG, PNG, GIF (max: 2MB)
                            </p>
                        </div>
                    </form>

                    @if($user->avatar_url)
                        <button type="button" onclick="confirmDeleteAvatar()"
                                class="w-full px-4 py-2 text-sm font-medium text-red-600 bg-red-100 border border-red-300 rounded-lg hover:bg-red-200">
                            <i class="mr-2 fas fa-trash"></i>Hapus Foto
                        </button>
                    @endif
                </div>
            </div>

            <!-- Account Info Card -->
            <div class="p-6 bg-white rounded-xl shadow-soft">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Informasi Akun</h3>

                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Role</label>
                        <p class="mt-1 text-sm text-gray-900 capitalize">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ $user->role }}
                            </span>
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-600">Bergabung Pada</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $user->created_at ? $user->created_at->format('d F Y') : '-' }}
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-600">Terakhir Diperbarui</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $user->updated_at ? $user->updated_at->format('d F Y H:i') : '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Forms -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Profile Information Form -->
            <div class="p-6 bg-white rounded-xl shadow-soft">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Informasi Profil</h3>

                <form action="{{ route('admin.profile.update-profile') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                class="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror">
                            @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required
                                class="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('username') border-red-300 @enderror"
                                placeholder="tanpa spasi, gunakan huruf/angka/garis-bawah-tengah">
                            @error('username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                required
                                class="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-300 @enderror">
                            @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-4">
                            <button type="submit"
                                class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <i class="mr-2 fas fa-save"></i>Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Change Password Form -->
            <div class="p-6 bg-white rounded-xl shadow-soft">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Ubah Password</h3>

                <form action="{{ route('admin.profile.update-password') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700">Password Saat
                                Ini</label>
                            <input type="password" id="current_password" name="current_password" required
                                class="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('current_password') border-red-300 @enderror">
                            @error('current_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                            <input type="password" id="password" name="password" required
                                class="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-300 @enderror">
                            @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation"
                                class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                class="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="pt-4">
                            <button type="submit"
                                class="px-4 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                <i class="mr-2 fas fa-key"></i>Ubah Password
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Avatar Confirmation Modal -->
<div id="delete-avatar-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div
            class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div
                        class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                        <i class="text-red-600 fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">
                            Hapus Foto Profil
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Apakah Anda yakin ingin menghapus foto profil? Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                <form action="{{ route('admin.profile.delete-avatar') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Hapus
                    </button>
                </form>
                <button type="button" onclick="closeDeleteModal()"
                    class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('avatar-preview');
            const initials = document.getElementById('avatar-initials');
            if (img) {
                img.src = e.target.result;
                img.classList.remove('hidden');
            }
            if (initials) {
                initials.classList.add('hidden');
            }
        }
        reader.readAsDataURL(input.files[0]);
        // Auto submit form after file selection
        document.getElementById('avatar-form').submit();
    }
}

function confirmDeleteAvatar() {
    document.getElementById('delete-avatar-modal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('delete-avatar-modal').classList.add('hidden');
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('delete-avatar-modal');
    if (event.target === modal) {
        closeDeleteModal();
    }
});

// Handle escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDeleteModal();
    }
});
</script>
@endpush
