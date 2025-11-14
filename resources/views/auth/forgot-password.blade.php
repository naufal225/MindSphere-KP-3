<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - EduTrack</title>
     @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-6">
        <div class="text-center mb-6">
            <i class="fas fa-key text-blue-500 text-4xl mb-3"></i>
            <h1 class="text-2xl font-bold text-gray-800">Lupa Password</h1>
            <p class="text-gray-600 mt-2">Masukkan email akun Anda untuk reset password</p>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <div class="flex">
                    <div class="py-1">
                        <i class="fas fa-exclamation-circle mr-3"></i>
                    </div>
                    <div>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <div class="flex">
                    <div class="py-1">
                        <i class="fas fa-check-circle mr-3"></i>
                    </div>
                    <div>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email Akun
                </label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    required
                    value="{{ old('email') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="masukkan email Anda">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                <i class="fas fa-paper-plane mr-2"></i>
                Kirim Link Reset Password
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Ingat password?
                <a href="#" onclick="closeWindow()" class="text-blue-500 hover:text-blue-700 font-medium">
                    Kembali ke login
                </a>
            </p>
        </div>
    </div>

    <script>
        function closeWindow() {
            window.close();
        }
    </script>
</body>
</html>
