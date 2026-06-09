<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Login') }} - FoodDelivery Pro</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4f46e5',
                        primary_dark: '#4338ca',
                        success: '#10b981',
                        danger: '#ef4444',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Laravel asset -->
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }
    </style>
    <!-- Cairo Font (Arabic RTL) -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        html[dir="rtl"] body {
            font-family: 'Cairo', sans-serif !important;
        }

        html[dir="rtl"] .ml-3 {
            margin-left: 0 !important;
            margin-right: 0.75rem !important;
        }

        html[dir="rtl"] .ml-4 {
            margin-left: 0 !important;
            margin-right: 1rem !important;
        }

        html[dir="rtl"] aside {
            left: auto !important;
            right: 0 !important;
            border-right: none !important;
            border-left: 1px solid #e5e7eb !important;
        }

        html[dir="rtl"] .space-x-4> :not([hidden])~ :not([hidden]) {
            --tw-space-x-reverse: 1 !important;
        }

        html[dir="rtl"] .space-x-2> :not([hidden])~ :not([hidden]) {
            --tw-space-x-reverse: 1 !important;
        }

        html[dir="rtl"] #langDropdownMenu,
        html[dir="rtl"] #profileDropdownMenu {
            right: auto !important;
            left: 0 !important;
        }
    </style>
</head>

<body class="bg-white text-gray-800 font-sans antialiased min-h-screen flex">

    <!-- Toast Alert -->
    <div id="toast"
        class="hidden-el fixed top-4 right-4 z-50 rounded-md bg-red-50 p-4 shadow-lg border border-red-200 transition-opacity duration-300">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800" id="toastMessage">{{ __('Invalid credentials provided.') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Left Column: Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
        <div class="w-full max-w-md animate-fade-in-up">

            <!-- Brand Logo -->
            <div class="flex justify-center mb-8">
                <div class="bg-[#4338ca]/10 p-3 rounded-full">
                    <svg class="w-10 h-10 text-[#4338ca]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>

            <h2 class="text-3xl font-bold text-center text-gray-900 mb-2">{{ __('Welcome Back') }}</h2>
            <p class="text-sm text-gray-500 text-center mb-8">{{ __('Please enter your details to sign in.') }}</p>

            <form action="{{ route('admin.login.submit') }}" method="POST" autocomplete="off">
                @csrf

                <div class="space-y-5">
                    <div>
                        <label for="email"
                            class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email or Phone') }}</label>
                        <input type="text" name="email" id="email" required autocomplete="off"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#4338ca] focus:ring-[#4338ca] sm:text-sm h-11 px-4 border bg-gray-50 focus:bg-white transition-colors"
                            placeholder="{{ __('Enter your email') }}">
                    </div>

                    <div>
                        <label for="password"
                            class="block text-sm font-medium text-gray-700 mb-1">{{ __('Password') }}</label>
                        <input type="password" name="password" id="password" required autocomplete="new-password"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#4338ca] focus:ring-[#4338ca] sm:text-sm h-11 px-4 border bg-gray-50 focus:bg-white transition-colors"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between mt-5 mb-8">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 text-[#4338ca] focus:ring-[#4338ca] cursor-pointer">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-700 cursor-pointer">
                            {{ __('Remember me') }}
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="{{ route('admin.password.request.index') }}"
                            class="font-medium text-[#4338ca] hover:text-[#3730a3] hover:underline transition-colors">
                            {{ __('Forgot password?') }}
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-[#4338ca] hover:bg-[#3730a3] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#4338ca] transition-colors">
                        {{ __('Sign In to Dashboard') }}
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- Right Column: Branding & Image -->
    <div class="hidden lg:flex lg:w-1/2 relative bg-gray-900">
        <!-- Background Image -->
        <img src="https://images.unsplash.com/photo-1526367790999-0150786686a2?q=80&w=1080&auto=format&fit=crop"
            alt="Food Delivery Background" class="absolute inset-0 w-full h-full object-cover">

        <!-- Overlay -->
        <div class="absolute inset-0 bg-[#4338ca]/80 mix-blend-multiply"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/50 to-transparent"></div>

        <!-- Content -->
        <div class="relative z-10 w-full flex items-center justify-center p-12">
            <h1 class="text-4xl lg:text-5xl font-bold text-white text-center drop-shadow-lg leading-tight">
                Restaurant Delivery<br>{{ __('Management System') }}
            </h1>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/login.js') }}"></script>
</body>

</html>