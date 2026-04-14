<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FoodDelivery Pro</title>
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
    <link rel="stylesheet" href="{{ asset('modules/admin/css/auth.css') }}">
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased min-h-screen flex items-center justify-center p-4">

    <!-- Toast Alert -->
    <div id="toast" class="hidden-el fixed top-4 right-4 z-50 rounded-md bg-red-50 p-4 shadow-lg border border-red-200 transition-opacity duration-300">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800" id="toastMessage">Invalid credentials provided.</p>
            </div>
        </div>
    </div>

    <!-- Login Card -->
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl border border-gray-100 overflow-hidden fade-in">
        <div class="px-8 py-10">
            <!-- Brand Logo -->
            <div class="flex justify-center mb-8">
                <div class="bg-primary/10 p-3 rounded-full">
                    <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
            </div>

            <h2 class="text-2xl font-bold text-center text-gray-900 mb-2">Welcome Back</h2>
            <p class="text-sm text-gray-500 text-center mb-8">Please enter your details to sign in.</p>

            @if($errors->any() || session('error'))
                <div class="mb-4 rounded-md bg-red-50 p-4 border border-red-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Oops! There were some problems with your login.</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @if(session('error'))
                                        <li>{{ session('error') }}</li>
                                    @endif
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email or Phone</label>
                        <input type="text" name="email" id="email" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-11 px-4 border bg-gray-50 focus:bg-white transition-colors" placeholder="Enter your email">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" id="password" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-11 px-4 border bg-gray-50 focus:bg-white transition-colors" placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between mt-5 mb-8">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-700 cursor-pointer">Remember me</label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-primary hover:text-primary_dark hover:underline transition-colors">Forgot password?</a>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-primary hover:bg-primary_dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                        Sign In
                    </button>
                </div>
            </form>
        </div>
        
        <div class="bg-gray-50 px-8 py-5 border-t border-gray-100 text-center">
            <p class="text-sm text-gray-600">
                Don't have an account? 
                <a href="{{ route('admin.register') }}" class="font-bold text-primary hover:text-primary_dark hover:underline transition-colors ml-1">Sign up now</a>
            </p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('modules/admin/js/login.js') }}"></script>
</body>
</html>
