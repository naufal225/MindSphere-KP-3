<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Terkirim - EduTrack</title>
     @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-6 text-center">
        <div class="mb-4">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-envelope text-green-500 text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Email Terkirim!</h1>
            <p class="text-gray-600 mt-2">Link reset password telah dikirim ke email Anda</p>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 mr-2 mt-1"></i>
                <div class="text-left">
                    <p class="text-blue-700 text-sm">
                        <strong>Periksa inbox email Anda</strong> dan klik link reset password yang kami kirimkan.
                    </p>
                    <p class="text-blue-600 text-xs mt-2">
                        <i class="fas fa-clock mr-1"></i>
                        Link berlaku selama 30 menit
                    </p>
                    <p class="text-blue-600 text-xs mt-1">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Jangan bagikan link ini kepada siapapun
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <button onclick="closeWindow()"
                    class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                <i class="fas fa-times mr-2"></i>
                Tutup Halaman
            </button>

            <p class="text-xs text-gray-500 mt-4">
                Tidak menerima email?
                <a href="{{ route('password.request') }}" class="text-blue-500 hover:text-blue-700">
                    Kirim ulang
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
