<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - EduTrack</title>
     @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-6">
        <div class="text-center mb-6">
            <i class="fas fa-key text-blue-500 text-4xl mb-3"></i>
            <h1 class="text-2xl font-bold text-gray-800">Reset Password</h1>
            <p class="text-gray-600 mt-2">Buat password baru untuk akun Anda</p>
        </div>

        @if(isset($error))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <div class="flex">
                    <div class="py-1">
                        <i class="fas fa-exclamation-circle mr-3"></i>
                    </div>
                    <div>
                        <p class="text-sm">{{ $error }}</p>
                        @if(!$valid)
                        <p class="text-xs mt-1">Silakan request reset password lagi.</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if(!isset($error) || $valid)
        <!-- Info Email -->
        <div class="bg-blue-50 border border-blue-200 rounded-md p-3 mb-4">
            <div class="flex items-center">
                <i class="fas fa-envelope text-blue-500 mr-2"></i>
                <div>
                    <p class="text-blue-700 text-sm font-medium">Reset password untuk:</p>
                    <p class="text-blue-600 text-sm">{{ $email }}</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Password Baru
                </label>
                <div class="relative">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Masukkan password baru">
                    <button type="button"
                            onclick="togglePassword('password')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <i class="fas fa-eye text-gray-400"></i>
                    </button>
                </div>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                    Konfirmasi Password Baru
                </label>
                <div class="relative">
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Konfirmasi password baru">
                    <button type="button"
                            onclick="togglePassword('password_confirmation')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <i class="fas fa-eye text-gray-400"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between text-sm text-gray-600">
                <div class="flex items-center">
                    <i class="fas fa-clock mr-1"></i>
                    <span>Token berlaku 30 menit</span>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                <i class="fas fa-sync-alt mr-2"></i>
                Reset Password
            </button>
        </form>
        @endif

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                <a href="{{ route('password.request') }}" class="text-blue-500 hover:text-blue-700 font-medium">
                    Request reset password lagi
                </a>
            </p>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling.querySelector('i');

            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'fas fa-eye-slash text-gray-400';
            } else {
                field.type = 'password';
                icon.className = 'fas fa-eye text-gray-400';
            }
        }
    </script>
</body>
</html>
