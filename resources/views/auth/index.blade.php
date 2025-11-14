<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KeepItGrow</title>
     @vite('resources/css/app.css')
    <link rel="icon" type="image/x-icon" href="{{ asset('yaztech-icon.jpg') }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .orbit {
            position: absolute;
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 50%;
        }

        .orbit-1 {
            width: 70px;
            height: 70px;
        }

        .orbit-2 {
            width: 80px;
            height: 80px;
        }

        .leaf {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 16px;
            height: 16px;
            background: #22C55E;
            border-radius: 50% 0 50% 50%;
            box-shadow: 0 2px 4px rgba(34, 197, 94, 0.3);
        }

        .star {
            position: absolute;
            bottom: -5px;
            left: -5px;
            width: 12px;
            height: 12px;
            background: #F59E0B;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);
        }

        .input-focus-effect:focus {
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            border-color: #2563EB;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563EB 0%, #3B82F6 100%);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6B7280 0%, #9CA3AF 100%);
            box-shadow: 0 4px 12px rgba(107, 114, 128, 0.2);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(107, 114, 128, 0.3);
        }

        .capslock-warning {
            animation: fadeIn 0.3s ease;
        }

        .card-glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .floating-element {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .gradient-text {
            background: linear-gradient(135deg, #2563EB 0%, #3B82F6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <!-- Background Elements -->
    <div class="fixed top-10 left-10 floating-element">
        <div class="relative">
            <div class="orbit orbit-1"></div>
            <div class="leaf"></div>
        </div>
    </div>

    <div class="fixed bottom-10 right-10 floating-element" style="animation-delay: 2s;">
        <div class="relative">
            <div class="orbit orbit-2"></div>
            <div class="star"></div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="w-full max-w-md">
        <div class="overflow-hidden card-glass rounded-2xl">
            <!-- Header Section -->
            <div class="px-8 pt-10 pb-6 text-center">
                <!-- Logo with KeepItGrow branding -->
                <div class="flex justify-center mb-4">
                    <div class="relative">
                        <img src="{{ asset('img/logo.png') }}" alt="KeepItGrow Logo" class="w-20 h-20">
                        <div class="absolute -inset-2 bg-blue-100 rounded-full blur-sm opacity-50 -z-10"></div>
                    </div>
                </div>
                <h1 class="mb-2 text-3xl font-bold gradient-text">KeepItGrow</h1>
            </div>

            <!-- Form Section -->
            <div class="px-8 pb-8">
                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="p-4 mb-6 border border-red-200 rounded-xl bg-red-50">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    Login failed:
                                </h3>
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

                <!-- Success Message -->
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

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block mb-2 text-sm font-semibold text-gray-700">
                            Email Address
                        </label>
                        <div class="relative">
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl input-focus-effect transition-all duration-200 bg-white/80 backdrop-blur-sm @error('email') border-red-500 @enderror"
                                placeholder="Enter your email"
                                required
                            >
                            <!-- Email Icon -->
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Password Field with Toggle -->
                    <div>
                        <label for="password" class="block mb-2 text-sm font-semibold text-gray-700">
                            Password
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="w-full px-4 py-3 pl-11 pr-11 border border-gray-300 rounded-xl input-focus-effect transition-all duration-200 bg-white/80 backdrop-blur-sm @error('password') border-red-500 @enderror"
                                placeholder="Enter your password"
                                required
                            >
                            <!-- Lock Icon -->
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <!-- Toggle Password Button -->
                            <button
                                type="button"
                                id="togglePassword"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 transition-colors duration-200 hover:text-gray-600 focus:outline-none focus:text-gray-600"
                                aria-label="Toggle password visibility"
                            >
                                <!-- Eye Icon (Show Password) -->
                                <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <!-- Eye Off Icon (Hide Password) -->
                                <svg id="eyeOffIcon" class="hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input
                                type="checkbox"
                                id="remember"
                                name="remember"
                                class="w-4 h-4 text-blue-600 transition-colors bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                {{ old('remember') ? 'checked' : '' }}
                            >
                            <label for="remember" class="ml-3 text-sm font-medium text-gray-700 cursor-pointer">
                                Remember me
                            </label>
                        </div>

                        <a href="{{ route('password.request') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors duration-200">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Login Button -->
                    <button
                        type="submit"
                        id="loginBtn"
                        class="w-full btn-primary text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                    >
                        <span id="loginBtnContent" class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Sign In
                        </span>
                        <!-- Loading Spinner -->
                        <span id="loginBtnLoading" class="flex items-center justify-center hidden">
                            <svg class="w-5 h-5 mr-3 -ml-1 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Signing In...
                        </span>
                    </button>
                </form>

                
            </div>
        </div>
    </div>

    <!-- JavaScript for Enhanced Functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password Toggle Functionality
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeOffIcon = document.getElementById('eyeOffIcon');

            togglePassword.addEventListener('click', function() {
                // Toggle password visibility
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Toggle eye icons with smooth transition
                eyeIcon.classList.toggle('hidden');
                eyeOffIcon.classList.toggle('hidden');

                // Add visual feedback
                this.classList.add('scale-110');
                setTimeout(() => {
                    this.classList.remove('scale-110');
                }, 150);

                // Update aria-label for accessibility
                const isPasswordVisible = type === 'text';
                this.setAttribute('aria-label', isPasswordVisible ? 'Hide password' : 'Show password');
            });

            // Enhanced Focus Effects
            const inputs = document.querySelectorAll('input[type="email"], input[type="password"]');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.classList.add('input-focus-effect');
                    this.parentElement.classList.add('ring-2', 'ring-blue-100');
                });

                input.addEventListener('blur', function() {
                    this.classList.remove('input-focus-effect');
                    this.parentElement.classList.remove('ring-2', 'ring-blue-100');
                });
            });

            // Form Submission with Loading State
            const loginForm = document.querySelector('form');
            const loginBtn = document.getElementById('loginBtn');
            const loginBtnContent = document.getElementById('loginBtnContent');
            const loginBtnLoading = document.getElementById('loginBtnLoading');

            loginForm.addEventListener('submit', function() {
                // Show loading state
                loginBtn.disabled = true;
                loginBtnContent.classList.add('hidden');
                loginBtnLoading.classList.remove('hidden');
            });

            // Real-time Validation
            const emailInput = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            emailInput.addEventListener('input', function() {
                const isValid = emailRegex.test(this.value);
                if (this.value.length > 0) {
                    if (isValid) {
                        this.classList.remove('border-red-500');
                        this.classList.add('border-green-500');
                    } else {
                        this.classList.remove('border-green-500');
                        this.classList.add('border-red-500');
                    }
                } else {
                    this.classList.remove('border-red-500', 'border-green-500');
                }
            });

            passwordInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    if (this.value.length >= 6) {
                        this.classList.remove('border-red-500');
                        this.classList.add('border-green-500');
                    } else {
                        this.classList.remove('border-green-500');
                        this.classList.add('border-red-500');
                    }
                } else {
                    this.classList.remove('border-red-500', 'border-green-500');
                }
            });

            // Keyboard Shortcuts
            document.addEventListener('keydown', function(e) {
                // Alt + P to toggle password visibility
                if (e.altKey && e.key === 'p') {
                    e.preventDefault();
                    togglePassword.click();
                }

                // Enter key to submit form when focused on any input
                if (e.key === 'Enter' && (e.target === emailInput || e.target === passwordInput)) {
                    loginForm.submit();
                }
            });

            // Auto-focus on email input
            emailInput.focus();

            // Caps Lock Detection
            let capsLockWarning = null;

            passwordInput.addEventListener('keydown', function(e) {
                if (e.getModifierState && e.getModifierState('CapsLock')) {
                    if (!capsLockWarning) {
                        capsLockWarning = document.createElement('div');
                        capsLockWarning.className = 'mt-1 text-sm text-amber-600 flex items-center capslock-warning';
                        capsLockWarning.innerHTML = `
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            Caps Lock is on
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

            // Remove caps lock warning when input loses focus
            passwordInput.addEventListener('blur', function() {
                if (capsLockWarning) {
                    capsLockWarning.remove();
                    capsLockWarning = null;
                }
            });

            // Add hover effects to buttons
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
