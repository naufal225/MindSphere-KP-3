<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KeepItGrow</title>
    @vite('resources/css/app.css')
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo.png') }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        :root {
            --primary: #2563EB;
            --primary-2: #3B82F6;
            --accent: #22C55E;
            --bg: #F3F4F6;
        }

        body {
            font-family: 'Space Grotesk', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background:
                radial-gradient(80% 60% at 20% 20%, rgba(37, 99, 235, 0.08), transparent),
                radial-gradient(70% 50% at 80% 0%, rgba(59, 130, 246, 0.08), transparent),
                var(--bg);
            color: #0f172a;
        }

        .card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 18px 38px -24px rgba(15, 23, 42, 0.25);
        }

        .input-focus-effect:focus {
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
            border-color: var(--primary);
            background: #fff;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-2) 100%);
            box-shadow: 0 12px 28px -16px rgba(37, 99, 235, 0.55);
            transition: transform 0.18s ease, box-shadow 0.22s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px -16px rgba(37, 99, 235, 0.65);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(148, 163, 184, 0.4), transparent);
        }

        .capslock-warning {
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="min-h-screen flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md card rounded-2xl p-8">
            <div class="flex flex-col items-center text-center mb-8 space-y-4">
                <div class="h-14 w-14 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-center">
                    <img src="{{ asset('img/logo.png') }}" alt="KeepItGrow Logo" class="h-10 w-10 object-contain">
                </div>
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">Masuk ke KeepItGrow</h1>
                    <p class="text-sm text-slate-500 mt-1">Gunakan email dan password terdaftar.</p>
                </div>
            </div>

            @if ($errors->any())
                <div class="p-4 mb-6 border border-red-200 rounded-xl bg-red-50">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-semibold text-red-800">Login gagal:</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="space-y-1 list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="p-4 mb-6 border border-green-200 rounded-xl bg-green-50">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block mb-2 text-sm font-semibold text-slate-700">
                        Email
                    </label>
                    <div class="relative">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="w-full px-4 py-3 pl-12 border border-slate-200 rounded-2xl input-focus-effect transition-all duration-200 bg-slate-50 @error('email') border-red-500 @enderror"
                            placeholder="nama@domain.com"
                            required
                        >
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="password" class="block mb-2 text-sm font-semibold text-slate-700">
                        Password
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="w-full px-4 py-3 pl-12 pr-12 border border-slate-200 rounded-2xl input-focus-effect transition-all duration-200 bg-slate-50 @error('password') border-red-500 @enderror"
                            placeholder="Gunakan password yang aman"
                            required
                        >
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <button
                            type="button"
                            id="togglePassword"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 transition-colors duration-200 hover:text-slate-600 focus:outline-none focus:text-slate-600"
                            aria-label="Toggle password visibility"
                        >
                            <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eyeOffIcon" class="hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center space-x-3 text-sm font-medium text-slate-700 cursor-pointer">
                        <input
                            type="checkbox"
                            id="remember"
                            name="remember"
                            class="w-4 h-4 text-[#2563EB] transition-colors bg-white border-slate-300 rounded focus:ring-[#2563EB] focus:ring-2"
                            {{ old('remember') ? 'checked' : '' }}
                        >
                        <span>Ingat saya</span>
                    </label>

                    <a href="{{ route('password.request') }}" class="text-sm font-semibold text-[#2563EB] hover:text-[#1d4ed8] transition-colors duration-200">
                        Lupa password?
                    </a>
                </div>

                <button
                    type="submit"
                    id="loginBtn"
                    class="w-full btn-primary text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2 focus:ring-offset-white disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                >
                    <span id="loginBtnContent" class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Masuk sekarang
                    </span>
                    <span id="loginBtnLoading" class="flex items-center justify-center hidden">
                        <svg class="w-5 h-5 mr-3 -ml-1 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses...
                    </span>
                </button>

                <div class="divider"></div>
                <p class="text-xs text-slate-500 text-center">
                    Pastikan kredensial Anda aman dan rahasia.
                </p>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeOffIcon = document.getElementById('eyeOffIcon');

            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                eyeIcon.classList.toggle('hidden');
                eyeOffIcon.classList.toggle('hidden');

                this.classList.add('scale-110');
                setTimeout(() => {
                    this.classList.remove('scale-110');
                }, 150);

                const isPasswordVisible = type === 'text';
                this.setAttribute('aria-label', isPasswordVisible ? 'Hide password' : 'Show password');
            });

            const inputs = document.querySelectorAll('input[type="email"], input[type="password"]');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.classList.add('input-focus-effect');
                    this.parentElement.classList.add('ring-2', 'ring-sky-100');
                });

                input.addEventListener('blur', function() {
                    this.classList.remove('input-focus-effect');
                    this.parentElement.classList.remove('ring-2', 'ring-sky-100');
                });
            });

            const loginForm = document.querySelector('form');
            const loginBtn = document.getElementById('loginBtn');
            const loginBtnContent = document.getElementById('loginBtnContent');
            const loginBtnLoading = document.getElementById('loginBtnLoading');

            loginForm.addEventListener('submit', function() {
                loginBtn.disabled = true;
                loginBtnContent.classList.add('hidden');
                loginBtnLoading.classList.remove('hidden');
            });

            const emailInput = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            emailInput.addEventListener('input', function() {
                const isValid = emailRegex.test(this.value);
                if (this.value.length > 0) {
                    if (isValid) {
                        this.classList.remove('border-red-500');
                        this.classList.add('border-emerald-500');
                    } else {
                        this.classList.remove('border-emerald-500');
                        this.classList.add('border-red-500');
                    }
                } else {
                    this.classList.remove('border-red-500', 'border-emerald-500');
                }
            });

            passwordInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    if (this.value.length >= 6) {
                        this.classList.remove('border-red-500');
                        this.classList.add('border-emerald-500');
                    } else {
                        this.classList.remove('border-emerald-500');
                        this.classList.add('border-red-500');
                    }
                } else {
                    this.classList.remove('border-red-500', 'border-emerald-500');
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.altKey && e.key === 'p') {
                    e.preventDefault();
                    togglePassword.click();
                }

                if (e.key === 'Enter' && (e.target === emailInput || e.target === passwordInput)) {
                    loginForm.submit();
                }
            });

            emailInput.focus();

            let capsLockWarning = null;

            passwordInput.addEventListener('keydown', function(e) {
                if (e.getModifierState && e.getModifierState('CapsLock')) {
                    if (!capsLockWarning) {
                        capsLockWarning = document.createElement('div');
                        capsLockWarning.className = 'mt-2 text-sm text-amber-600 flex items-center capslock-warning';
                        capsLockWarning.innerHTML = `
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            Caps Lock aktif
                        `;
                        this.parentElement.appendChild(capsLockWarning);
                    }
                } else {
                    if (capsLockWarning) {
                        capsLockWarning.remove();
                        capsLockWarning = null;
                    }
                }
            });

            passwordInput.addEventListener('blur', function() {
                if (capsLockWarning) {
                    capsLockWarning.remove();
                    capsLockWarning = null;
                }
            });

            const buttons = document.querySelectorAll('button, a');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-1px)';
                });
                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>
