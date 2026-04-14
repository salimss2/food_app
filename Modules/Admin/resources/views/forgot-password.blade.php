<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - FoodDelivery Pro</title>
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
    <div id="toast" class="hidden-el fixed top-4 right-4 z-50 rounded-md bg-green-50 p-4 shadow-lg border border-green-200 transition-opacity duration-300">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800" id="toastMessage">OTP sent successfully.</p>
            </div>
        </div>
    </div>

    <!-- Recovery Card -->
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl border border-gray-100 overflow-hidden fade-in">
        <div class="px-8 py-10">
            <!-- Back Arrow icon routing to Login -->
            <a href="{{ route('admin.login') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors mb-6 group">
                <svg class="w-4 h-4 mr-1 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Login
            </a>

            <!-- Lock Icon -->
            <div class="flex justify-start mb-6">
                <div class="bg-indigo-50 p-3 rounded-xl border border-indigo-100">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                </div>
            </div>

            <h2 class="text-2xl font-bold text-gray-900 mb-2">Forgot Password?</h2>
            <p class="text-sm text-gray-500 mb-8">No worries, we'll send you reset instructions. Please enter your associated email or phone number.</p>

            <form onsubmit="handleRecovery(event)" action="#" method="POST">
                
                <div class="mb-6">
                    <label for="recovery_identifier" class="block text-sm font-medium text-gray-700 mb-1">Email or Phone</label>
                    <input type="text" name="recovery_identifier" id="recovery_identifier" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-11 px-4 border bg-gray-50 focus:bg-white transition-colors" placeholder="e.g. john@example.com">
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-primary hover:bg-primary_dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                        Send OTP
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('modules/admin/js/forgot-password.js') }}"></script>
</body>
</html>
