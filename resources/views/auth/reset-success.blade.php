<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Berhasil Direset - EduTrack</title>
     @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-6 text-center">
        <div class="mb-4">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-check text-green-500 text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Password Berhasil Direset!</h1>
            <p class="text-gray-600 mt-2">Password Anda telah berhasil diubah.</p>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
            <div class="flex items-center justify-center">
                <i class="fas fa-info-circle text-green-500 mr-2"></i>
                <p class="text-green-700 text-sm">
                    Anda sekarang dapat login ke aplikasi mobile dengan password baru.
                </p>
            </div>
        </div>

        <div class="space-y-3">
            <button onclick="closeWindow()"
                    class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                <i class="fas fa-times mr-2"></i>
                Tutup Halaman
            </button>

            <p class="text-xs text-gray-500 mt-4">
                Halaman ini akan otomatis tertutup dalam <span id="countdown">5</span> detik
            </p>
        </div>
    </div>

    <script>
        function closeWindow() {
            window.close();
        }

        // Auto close countdown
        let countdown = 5;
        const countdownElement = document.getElementById('countdown');

        const countdownInterval = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;

            if (countdown <= 0) {
                clearInterval(countdownInterval);
                window.close();
            }
        }, 1000);
    </script>
</body>
</html>
