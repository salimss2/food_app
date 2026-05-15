<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commissions Overview - Admin Dashboard</title>
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
                        danger: '#ef4444'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('modules/admin/css/app.css') }}">
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

<body class="bg-gray-50 text-gray-800 font-sans antialiased overflow-hidden flex h-screen">

    @include('admin::layouts.partials.sidebar')






    <!-- Layout Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300">

        @include('admin::layouts.partials.header')

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto w-full bg-gray-50">

            <!-- Sub Navigation Tabs (Mocks Laravel View Sub-Routing) -->
            <div class="bg-white border-b border-gray-200 px-4 sm:px-6 lg:px-8 pt-4">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="{{ route('admin.commissions.index') }}"
                        class="border-primary text-primary whitespace-nowrap border-b-2 py-4 px-1 text-sm font-bold">
                        Overview
                    </a>
                    <a href="{{ route('admin.commissions-restaurant.index') }}"
                        class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors">
                        Restaurant Commissions
                    </a>
                    <a href="{{ route('admin.commissions-driver.index') }}"
                        class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors">
                        Driver Commissions
                    </a>
                    <a href="{{ route('admin.commissions-settings.index') }}"
                        class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors">
                        {{ __('Enterprise Settings') }} (الإعدادات المتقدمة)
                    </a>
                </nav>
            </div>

            <div class="p-4 sm:p-6 lg:p-8">

                <div class="mb-6 flex flex-col justify-between items-start space-y-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Total System Earnings</h2>
                        <p class="text-sm text-gray-500 mt-1">High-level financial summaries generated from all
                            processed platform modules.</p>
                    </div>
                </div>

                <!-- Stats Overview -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
                    <!-- Total System Earnings -->
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 relative overflow-hidden group">
                        <div
                            class="absolute top-0 right-0 p-4 opacity-10 transform scale-150 group-hover:scale-110 transition-transform">
                            <svg class="w-24 h-24 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="flex items-center z-10 relative">
                            <div class="p-3 rounded-full bg-green-50 text-green-600 border border-green-100">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Gross Profit</p>
                                <h3 class="text-3xl font-extrabold text-gray-900 mt-1">$45,210.50</h3>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm z-10 relative">
                            <span class="text-green-600 font-bold flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                </svg>
                                12.5%
                            </span>
                            <span class="text-gray-500 ml-2">from last month</span>
                        </div>
                    </div>

                    <!-- Vendor Earnings -->
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 relative overflow-hidden group">
                        <div
                            class="absolute top-0 right-0 p-4 opacity-10 transform scale-150 group-hover:scale-110 transition-transform">
                            <svg class="w-24 h-24 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                                </path>
                            </svg>
                        </div>
                        <div class="flex items-center z-10 relative">
                            <div class="p-3 rounded-full bg-indigo-50 text-indigo-600 border border-indigo-100">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Restaurant Share
                                </p>
                                <h3 class="text-3xl font-extrabold text-gray-900 mt-1">$28,500.00</h3>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm z-10 relative">
                            <span class="text-gray-500 font-medium">Avg Commission: <span
                                    class="text-gray-900">15%</span></span>
                        </div>
                    </div>

                    <!-- Driver Earnings -->
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 relative overflow-hidden group">
                        <div
                            class="absolute top-0 right-0 p-4 opacity-10 transform scale-150 group-hover:scale-110 transition-transform">
                            <svg class="w-24 h-24 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z">
                                </path>
                                <path
                                    d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z">
                                </path>
                            </svg>
                        </div>
                        <div class="flex items-center z-10 relative">
                            <div class="p-3 rounded-full bg-yellow-50 text-yellow-600 border border-yellow-100">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Driver Share</p>
                                <h3 class="text-3xl font-extrabold text-gray-900 mt-1">$16,710.50</h3>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm z-10 relative">
                            <span class="text-gray-500 font-medium">Avg Commission: <span class="text-gray-900">10% +
                                    Tips</span></span>
                        </div>
                    </div>
                </div>

                <!-- Explanation Panel -->
                <div
                    class="bg-indigo-50 border border-indigo-100 rounded-xl p-6 text-indigo-900 flex flex-col md:flex-row items-center justify-between mb-8">
                    <div>
                        <h4 class="font-bold text-lg mb-1">Detailed Breakdowns Available</h4>
                        <p class="text-sm opacity-80">Navigate to the sub-sections to manage specific deductions,
                            configure rates, and review order-level commission details.</p>
                    </div>
                    <div class="mt-4 md:mt-0 flex space-x-3">
                        <a href="{{ route('admin.commissions-restaurant.index') }}"
                            class="px-4 py-2 border border-indigo-200 rounded-lg bg-white shadow-sm font-medium hover:bg-gray-50 transition-colors text-sm">Review
                            Vendors</a>
                        <a href="{{ route('admin.commissions-driver.index') }}"
                            class="px-4 py-2 border border-indigo-200 rounded-lg bg-white shadow-sm font-medium hover:bg-gray-50 transition-colors text-sm">Review
                            Fleet</a>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
</body>

</html>