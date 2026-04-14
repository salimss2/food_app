<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification - FoodDelivery Pro</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4f46e5',
                        primary_dark: '#4338ca',
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
                <p class="text-sm font-medium text-red-800" id="toastMessage">Invalid verification code.</p>
            </div>
        </div>
    </div>

    <!-- OTP Card -->
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl border border-gray-100 overflow-hidden fade-in">
        <div class="px-8 py-10">
            <!-- Mail Icon -->
            <div class="flex justify-start mb-6">
                <div class="bg-indigo-50 p-3 rounded-xl border border-indigo-100">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
            </div>

            <h2 class="text-2xl font-bold text-gray-900 mb-2">Check your inbox</h2>
            <p class="text-sm text-gray-500 mb-8 max-w-sm">We sent a 6-digit verification code to your contact details. Please enter it below.</p>

            <form onsubmit="handleVerification(event)" action="#" method="POST">
                
                <div class="flex justify-between items-center mb-6 space-x-2" id="otp-container">
                    <input type="text" maxlength="1" class="w-12 h-14 text-center text-xl font-bold rounded-lg border border-gray-300 focus:border-primary focus:ring-1 focus:ring-primary shadow-sm outline-none transition-all" data-index="0" autofocus>
                    <input type="text" maxlength="1" class="w-12 h-14 text-center text-xl font-bold rounded-lg border border-gray-300 focus:border-primary focus:ring-1 focus:ring-primary shadow-sm outline-none transition-all" data-index="1">
                    <input type="text" maxlength="1" class="w-12 h-14 text-center text-xl font-bold rounded-lg border border-gray-300 focus:border-primary focus:ring-1 focus:ring-primary shadow-sm outline-none transition-all" data-index="2">
                    <span class="text-gray-300 text-lg">-</span>
                    <input type="text" maxlength="1" class="w-12 h-14 text-center text-xl font-bold rounded-lg border border-gray-300 focus:border-primary focus:ring-1 focus:ring-primary shadow-sm outline-none transition-all" data-index="3">
                    <input type="text" maxlength="1" class="w-12 h-14 text-center text-xl font-bold rounded-lg border border-gray-300 focus:border-primary focus:ring-1 focus:ring-primary shadow-sm outline-none transition-all" data-index="4">
                    <input type="text" maxlength="1" class="w-12 h-14 text-center text-xl font-bold rounded-lg border border-gray-300 focus:border-primary focus:ring-1 focus:ring-primary shadow-sm outline-none transition-all" data-index="5">
                </div>

                <div class="mb-8 text-center text-sm">
                    <span class="text-gray-500">Didn't receive the code?</span>
                    <button type="button" id="resendBtn" class="font-bold text-gray-400 cursor-not-allowed ml-1" disabled>
                        Resend Code in <span id="timerSpan">02:00</span>
                    </button>
                    <!-- Success hook for resend -->
                    <span id="resendHook" class="hidden-el text-green-600 font-bold ml-1 text-xs"><br>New Code Dispatched!</span>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-primary hover:bg-primary_dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                        Verify Account
                    </button>
                </div>
            </form>
        </div>
        
        <div class="bg-gray-50 px-8 py-4 border-t border-gray-100 text-center">
            <a href="{{ route('admin.login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                &larr; Back to Login
            </a>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('modules/admin/js/otp-verification.js') }}"></script>
</body>
</html>
