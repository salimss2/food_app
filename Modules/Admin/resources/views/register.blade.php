<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - FoodDelivery Pro</title>
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
<body class="bg-gray-50 text-gray-800 font-sans antialiased min-h-screen flex items-center justify-center p-4 py-12">

    <!-- Toast Alert -->
    <div id="toastError" class="hidden-el fixed top-4 right-4 z-50 rounded-md bg-red-50 p-4 shadow-lg border border-red-200 transition-opacity duration-300">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800" id="toastMessage">Registration failed. Try again.</p>
            </div>
        </div>
    </div>

    <!-- Reg Card -->
    <div class="bg-white w-full max-w-xl rounded-2xl shadow-xl border border-gray-100 overflow-hidden fade-in">
        
        <!-- Header -->
        <div class="pt-10 px-8 pb-6 text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Join FoodDelivery Pro</h2>
            <p class="text-sm text-gray-500">Sign up below to access your account dashboard.</p>
        </div>

        <!-- Role Tabs -->
        <div class="flex border-b border-gray-200 bg-gray-50 px-8">
            <button onclick="switchTab('customer')" id="btn-customer" class="flex-1 py-4 text-sm font-bold border-b-2 border-primary text-primary focus:outline-none transition-colors">
                I'm a Customer
            </button>
            <button onclick="switchTab('vendor')" id="btn-vendor" class="flex-1 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none transition-colors">
                I'm a Restaurant Owner
            </button>
        </div>

        <!-- FORM: Customer -->
        <div id="form-customer" class="px-8 py-8 fade-in">
            <form onsubmit="handleRegistration(event, 'customer')" action="#" method="POST">
                <div class="space-y-5">
                    <div>
                        <label for="c_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="name" id="c_name" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-11 px-4 border bg-gray-50 focus:bg-white transition-colors" placeholder="John Doe">
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="c_email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="email" id="c_email" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-11 px-4 border bg-gray-50 focus:bg-white transition-colors" placeholder="john@example.com">
                        </div>
                        <div>
                            <label for="c_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="tel" name="phone" id="c_phone" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-11 px-4 border bg-gray-50 focus:bg-white transition-colors" placeholder="+1 234 567 890">
                        </div>
                    </div>

                    <div>
                        <label for="c_password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" id="c_password" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-11 px-4 border bg-gray-50 focus:bg-white transition-colors" placeholder="••••••••">
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-primary hover:bg-primary_dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                        Create Customer Account
                    </button>
                    <p class="text-xs text-center text-gray-400 mt-4">By signing up, you agree to our Terms of Service.</p>
                </div>
            </form>
        </div>

        <!-- FORM: Restaurant Vendor -->
        <div id="form-vendor" class="px-8 py-8 hidden-el fade-in">
            <form onsubmit="handleRegistration(event, 'vendor')" action="#" method="POST">
                <div class="space-y-5">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 border-b border-gray-100 pb-5">
                        <div class="sm:col-span-2">
                            <h4 class="text-sm font-bold text-gray-900 border-l-2 border-primary pl-2 uppercase tracking-wide">Owner Details</h4>
                        </div>
                        <div>
                            <label for="v_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="owner_name" id="v_name" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-11 px-4 border bg-gray-50 focus:bg-white transition-colors" placeholder="Owner Name">
                        </div>
                        <div>
                            <label for="v_email" class="block text-sm font-medium text-gray-700 mb-1">Business Email</label>
                            <input type="email" name="email" id="v_email" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-11 px-4 border bg-gray-50 focus:bg-white transition-colors" placeholder="hello@restaurant.com">
                        </div>
                        <div>
                            <label for="v_phone" class="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
                            <input type="tel" name="phone" id="v_phone" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-11 px-4 border bg-gray-50 focus:bg-white transition-colors">
                        </div>
                        <div>
                            <label for="v_password" class="block text-sm font-medium text-gray-700 mb-1">Account Password</label>
                            <input type="password" name="password" id="v_password" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-11 px-4 border bg-gray-50 focus:bg-white transition-colors" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2">
                        <div class="sm:col-span-2">
                            <h4 class="text-sm font-bold text-gray-900 border-l-2 border-indigo-400 pl-2 uppercase tracking-wide">Restaurant Details</h4>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="r_name" class="block text-sm font-medium text-gray-700 mb-1">Restaurant Name</label>
                            <input type="text" name="restaurant_name" id="r_name" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-11 px-4 border bg-gray-50 focus:bg-white transition-colors" placeholder="e.g. Papa's Pizzeria">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="r_address" class="block text-sm font-medium text-gray-700 mb-1">Physical Address</label>
                            <input type="text" name="restaurant_address" id="r_address" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-11 px-4 border bg-gray-50 focus:bg-white transition-colors" placeholder="Street, City, Zip">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="r_category" class="block text-sm font-medium text-gray-700 mb-1">Primary Food Category</label>
                            <select name="restaurant_category" id="r_category" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-11 px-4 border bg-gray-50 focus:bg-white transition-colors">
                                <option value="Fast Food">Fast Food</option>
                                <option value="Italian">Italian Cuisine</option>
                                <option value="Asian">Asian Fusion</option>
                                <option value="Healthy">Healthy / Vegan</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-gray-900 hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors">
                        Apply for Partner Vendor
                    </button>
                    <p class="text-xs text-center text-gray-400 mt-4">By signing up, you agree to our Vendor Terms.</p>
                </div>
            </form>
        </div>
        
        <!-- Footer -->
        <div class="bg-gray-100 px-8 py-5 text-center">
            <p class="text-sm text-gray-600">
                Already have an account? 
                <a href="{{ route('admin.login') }}" class="font-bold text-primary hover:text-primary_dark hover:underline transition-colors ml-1">Login here</a>
            </p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('modules/admin/js/register.js') }}"></script>
</body>
</html>
