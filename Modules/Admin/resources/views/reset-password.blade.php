<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - FoodDelivery Pro</title>
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
    <!-- Cairo Font (Arabic RTL) -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        html[dir="rtl"] body { font-family: 'Cairo', sans-serif !important; }
        html[dir="rtl"] .ml-3 { margin-left: 0 !important; margin-right: 0.75rem !important; }
        html[dir="rtl"] .ml-4 { margin-left: 0 !important; margin-right: 1rem !important; }
        html[dir="rtl"] aside { left: auto !important; right: 0 !important; border-right: none !important; border-left: 1px solid #e5e7eb !important; }
        html[dir="rtl"] .space-x-4 > :not([hidden]) ~ :not([hidden]) { --tw-space-x-reverse: 1 !important; }
        html[dir="rtl"] .space-x-2 > :not([hidden]) ~ :not([hidden]) { --tw-space-x-reverse: 1 !important; }
        html[dir="rtl"] #langDropdownMenu, html[dir="rtl"] #profileDropdownMenu { right: auto !important; left: 0 !important; }
    </style></head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased min-h-screen flex items-center justify-center p-4">

    <!-- Toast Alert -->
    <div id="toast" class="hidden-el fixed top-4 right-4 z-50 rounded-md bg-green-50 p-4 shadow-lg border border-green-200 transition-opacity duration-300">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800" id="toastMessage">Password updated successfully.</p>
            </div>
        </div>
    </div>

    <!-- Reset Card -->
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl border border-gray-100 overflow-hidden fade-in">
        <div class="px-8 py-10">

            <div class="flex justify-start mb-6">
                <div class="bg-indigo-50 p-3 rounded-xl border border-indigo-100">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
            </div>

            <h2 class="text-2xl font-bold text-gray-900 mb-2">Create New Password</h2>
            <p class="text-sm text-gray-500 mb-8">Your new password must be different from previous used passwords.</p>

            <form onsubmit="handleReset(event)" action="#" method="POST">
                
                <div class="space-y-5 mb-8">
                    <div>
                        <label for="newPwd" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" name="password" id="newPwd" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-11 px-4 border bg-gray-50 focus:bg-white transition-colors" placeholder="••••••••">
                        
                        <!-- Password Strength Indicator -->
                        <div class="mt-2 h-1.5 w-full bg-gray-200 rounded-full overflow-hidden flex">
                            <div id="pwdStr1" class="h-full w-1/3 transition-colors bg-gray-200"></div>
                            <div id="pwdStr2" class="h-full w-1/3 transition-colors bg-gray-200 border-l border-white"></div>
                            <div id="pwdStr3" class="h-full w-1/3 transition-colors bg-gray-200 border-l border-white"></div>
                        </div>
                        <p id="pwdFeedback" class="text-xs text-gray-500 mt-1">Include letters, numbers and symbols.</p>
                    </div>

                    <div>
                        <label for="confirmPwd" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="confirmPwd" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-11 px-4 border bg-gray-50 focus:bg-white transition-colors" placeholder="••••••••">
                        <p id="pwdMatchError" class="text-xs text-red-500 mt-1 hidden-el">Passwords do not match.</p>
                    </div>
                </div>

                <div>
                    <button type="submit" id="btnSubmit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-primary hover:bg-primary_dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all opacity-50 cursor-not-allowed" disabled>
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
        
        <div class="bg-gray-50 px-8 py-4 border-t border-gray-100 text-center">
            <a href="{{ route('admin.login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                Cancel
            </a>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('modules/admin/js/reset-password.js') }}"></script>
</body>
</html>
