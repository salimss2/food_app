<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Reports & Analytics') }} - {{ __('Admin Dashboard') }}</title>
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
                        warning: '#f59e0b',
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

            <div class="p-4 sm:p-6 lg:p-8">

                <div class="mb-6 flex flex-col justify-between items-start space-y-4">
                    <div class="flex justify-between items-center w-full">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ __('Platform Analytics') }}</h2>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ __('Exportable global metrics covering total revenue, active orders, and historical user aggregations.') }}
                            </p>
                        </div>
                        <div class="flex space-x-reverse space-x-3">
                            <a href="{{ route('admin.reports.export.pdf', request()->query()) }}"
                                class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow-sm text-sm font-bold hover:bg-gray-50 focus:outline-none transition-colors flex items-center cursor-pointer">
                                <svg class="w-4 h-4 me-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                {{ __('تصدير PDF') }}
                                <svg class="w-4 h-4 ms-2 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </a>
                            <a href="{{ route('admin.reports.export.csv', request()->query()) }}"
                                class="bg-primary border border-transparent text-white px-4 py-2 rounded-lg shadow-sm text-sm font-bold hover:bg-primary_dark focus:outline-none transition-colors flex items-center cursor-pointer">
                                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                                {{ __('تصدير CSV') }}
                                <svg class="w-4 h-4 ms-2 text-indigo-200" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Advanced Filters -->
                    <form method="GET" action="{{ route('admin.reports') }}" id="filterForm"
                        class="flex flex-wrap items-center gap-3 w-full border-t border-gray-200 pt-4 mt-2">
                        <div class="relative flex items-center space-x-reverse space-x-2">
                            <input type="date" name="start_date" value="{{ request('start_date') }}"
                                title="{{ __('من تاريخ') }}"
                                class="h-9 px-3 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary text-sm text-gray-600 bg-white">
                            <span class="text-sm text-gray-500">-</span>
                            <input type="date" name="end_date" value="{{ request('end_date') }}"
                                title="{{ __('إلى تاريخ') }}"
                                class="h-9 px-3 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary text-sm text-gray-600 bg-white">
                        </div>
                        <div class="relative">
                            <select name="restaurant_id"
                                class="h-9 px-3 pe-8 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary text-sm text-gray-600 bg-white appearance-none">
                                <option value="">{{ __('تصفية حسب المطعم/المنطقة') }}</option>
                                @foreach($restaurants ?? [] as $restaurant)
                                    <option value="{{ $restaurant->id }}" @if(request('restaurant_id') == $restaurant->id)
                                    selected @endif>{{ $restaurant->name }}</option>
                                @endforeach
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 end-0 flex items-center px-2 text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="relative">
                            <select name="payment_method"
                                class="h-9 px-3 pe-8 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary text-sm text-gray-600 bg-white appearance-none">
                                <option value="">{{ __('طريقة الدفع') }}</option>
                                <option value="all" @if(request('payment_method') == 'all') selected @endif>
                                    {{ __('الكل') }}
                                </option>
                                <option value="cash" @if(request('payment_method') == 'cash') selected @endif>
                                    {{ __('كاش') }}
                                </option>
                                <option value="bank_transfer" @if(request('payment_method') == 'bank_transfer') selected
                                @endif>{{ __('حوالة بنكية') }}</option>
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 end-0 flex items-center px-2 text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <button type="submit"
                                class="bg-primary text-white px-4 py-2 rounded-lg text-sm shadow-sm font-bold hover:bg-primary_dark transition-colors h-9 flex items-center">
                                {{ __('تطبيق الفلاتر') }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Toast Alert -->
                <div id="toast"
                    class="hidden-el fixed bottom-4 right-4 z-50 rounded-md bg-indigo-50 p-4 shadow-lg border border-indigo-200 transition-opacity duration-300">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-primary border-indigo-200" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ms-3">
                            <p class="text-sm font-medium text-indigo-900" id="toastMessage">{{ __('Success') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Global Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8 mt-4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Gross Revenue') }}
                            </h3>
                            <span class="inline-flex items-center p-1 rounded-md bg-green-50 text-green-600"><svg
                                    class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg></span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 mt-4">$<span
                                dir="ltr">{{ number_format($totalSales ?? 0, 2) }}</span></p>
                        <p class="text-sm text-green-600 font-medium mt-1 inline-flex items-center">
                            <svg class="w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                            +12.4% <span class="text-gray-400 font-normal ms-2 text-xs">{{ __('YTD') }}</span>
                        </p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Total Orders') }}
                            </h3>
                            <span class="inline-flex items-center p-1 rounded-md bg-blue-50 text-blue-600"><svg
                                    class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg></span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 mt-4"><span
                                dir="ltr">{{ number_format($ordersCount ?? 0) }}</span></p>
                        <p class="text-sm text-green-600 font-medium mt-1 inline-flex items-center">
                            <svg class="w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                            +5.2% <span class="text-gray-400 font-normal ms-2 text-xs">{{ __('vs last month') }}</span>
                        </p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Active Customers') }}
                            </h3>
                            <span class="inline-flex items-center p-1 rounded-md bg-purple-50 text-purple-600"><svg
                                    class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg></span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 mt-4"><span
                                dir="ltr">{{ number_format($activeCustomers ?? 0) }}</span></p>
                        <p class="text-sm text-green-600 font-medium mt-1 inline-flex items-center">
                            <svg class="w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                            +11.8% <span class="text-gray-400 font-normal ms-2 text-xs">{{ __('vs last month') }}</span>
                        </p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Avg Order Value') }}
                            </h3>
                            <span class="inline-flex items-center p-1 rounded-md bg-yellow-50 text-yellow-600"><svg
                                    class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg></span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 mt-4">$<span
                                dir="ltr">{{ number_format($averageOrderValue ?? 0, 2) }}</span></p>
                        <p class="text-sm text-red-600 font-medium mt-1 inline-flex items-center">
                            <svg class="w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                            -1.2% <span class="text-gray-400 font-normal ms-2 text-xs">{{ __('vs last month') }}</span>
                        </p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('مستحقات المطاعم') }}
                            </h3>
                            <span class="inline-flex items-center p-1 rounded-md bg-amber-50 text-amber-600"><svg
                                    class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg></span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 mt-4">$<span
                                dir="ltr">{{ number_format($pendingRestaurantPayouts ?? 0, 2) }}</span></p>
                        <p class="text-sm text-gray-500 font-medium mt-1 inline-flex items-center">
                            {{ __('Pending Payouts') }}
                        </p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('عهد الموصلين') }}
                            </h3>
                            <span class="inline-flex items-center p-1 rounded-md bg-cyan-50 text-cyan-600"><svg
                                    class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z">
                                    </path>
                                </svg></span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 mt-4">$<span
                                dir="ltr">{{ number_format($pendingDriverCash ?? 0, 2) }}</span></p>
                        <p class="text-sm text-gray-500 font-medium mt-1 inline-flex items-center">
                            {{ __('Pending Driver Cash') }}
                        </p>
                    </div>
                </div>

                <!-- Monthly Revenue Chart Area (Mock) -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 mb-8">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-900">{{ __('Revenue Trajectory over 6 Months') }}</h3>
                        <div class="flex flex-wrap space-x-reverse space-x-4">
                            <span class="inline-flex items-center text-xs font-semibold text-gray-600">
                                <span class="w-3 h-3 rounded-full bg-primary me-2"></span> {{ __('إجمالي المبيعات') }}
                                (GMV)
                            </span>
                            <span class="inline-flex items-center text-xs font-semibold text-gray-600 ms-4">
                                <span class="w-3 h-3 rounded-full bg-primary_dark me-2"></span> {{ __('عمولة المنصة') }}
                            </span>
                            <span class="inline-flex items-center text-xs font-semibold text-gray-600 ms-4">
                                <span class="w-3 h-3 rounded-full bg-gray-400 me-2"></span> {{ __('مستحقات المطاعم') }}
                            </span>
                            <span class="inline-flex items-center text-xs font-semibold text-gray-600 ms-4">
                                <span class="w-3 h-3 rounded-full bg-gray-200 me-2"></span> {{ __('أجور الموصلين') }}
                            </span>
                        </div>
                    </div>
                    <!-- CSS Mock Chart -->
                    <div
                        class="relative h-64 w-full flex items-end justify-between px-2 pt-10 border-t border-s border-gray-100">
                        <!-- Bars -->
                        <div class="w-[12%] bg-gray-100 h-[30%] rounded-t-sm relative group">
                            <div class="absolute bottom-0 w-full bg-primary h-[80%] rounded-t-sm transition-all group-hover:opacity-80 cursor-pointer"
                                title="{{ __('$42k Revenue') }}"></div>
                            <div class="absolute -bottom-6 w-full text-center text-xs text-gray-400 font-semibold">
                                {{ __('Jan') }}
                            </div>
                        </div>
                        <div class="w-[12%] bg-gray-100 h-[45%] rounded-t-sm relative group">
                            <div class="absolute bottom-0 w-full bg-primary h-[75%] rounded-t-sm transition-all group-hover:opacity-80 cursor-pointer"
                                title="{{ __('$55k Revenue') }}"></div>
                            <div class="absolute -bottom-6 w-full text-center text-xs text-gray-400 font-semibold">
                                {{ __('Feb') }}
                            </div>
                        </div>
                        <div class="w-[12%] bg-gray-100 h-[40%] rounded-t-sm relative group">
                            <div class="absolute bottom-0 w-full bg-primary h-[85%] rounded-t-sm transition-all group-hover:opacity-80 cursor-pointer"
                                title="{{ __('$51k Revenue') }}"></div>
                            <div class="absolute -bottom-6 w-full text-center text-xs text-gray-400 font-semibold">
                                {{ __('Mar') }}
                            </div>
                        </div>
                        <div class="w-[12%] bg-gray-100 h-[65%] rounded-t-sm relative group">
                            <div class="absolute bottom-0 w-full bg-primary h-[60%] rounded-t-sm transition-all group-hover:opacity-80 cursor-pointer"
                                title="{{ __('$75k Revenue') }}"></div>
                            <div class="absolute -bottom-6 w-full text-center text-xs text-gray-400 font-semibold">
                                {{ __('Apr') }}
                            </div>
                        </div>
                        <div class="w-[12%] bg-gray-100 h-[85%] rounded-t-sm relative group">
                            <div class="absolute bottom-0 w-full bg-primary h-[70%] rounded-t-sm transition-all group-hover:opacity-80 cursor-pointer"
                                title="{{ __('$95k Revenue') }}"></div>
                            <div class="absolute -bottom-6 w-full text-center text-xs text-gray-400 font-semibold">
                                {{ __('May') }}
                            </div>
                        </div>
                        <div class="w-[12%] bg-gray-100 h-[100%] rounded-t-sm relative group">
                            <div class="absolute bottom-0 w-full bg-primary h-[50%] rounded-t-sm transition-all group-hover:opacity-80 cursor-pointer"
                                title="{{ __('$120k Revenue') }}"></div>
                            <div class="absolute -bottom-6 w-full text-center text-xs text-gray-400 font-semibold">
                                {{ __('Jun') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ledger Details Table -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-6">
                    <div
                        class="px-6 py-5 border-b border-gray-200 flex justify-between items-center bg-gray-50 flex-col sm:flex-row space-y-3 sm:space-y-0">
                        <h3 class="text-lg font-bold text-gray-900">{{ __('Recent Accounting Ledgers') }}</h3>
                        <div class="flex items-center space-x-reverse space-x-3 w-full sm:w-auto">
                            <input type="text" id="reportSearch" placeholder="{{ __('Search report ID...') }}"
                                class="w-full sm:w-64 h-9 px-3 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary text-sm">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-start text-sm text-gray-600">
                            <thead class="bg-white border-b border-gray-200 font-semibold text-gray-700">
                                <tr>
                                    <th class="px-6 py-3 cursor-pointer hover:bg-gray-50 uppercase text-xs"
                                        onclick="sortData()">{{ __('Report ID') }} &#8693;</th>
                                    <th class="px-6 py-3 uppercase text-xs">{{ __('Date Range') }}</th>
                                    <th class="px-6 py-3 uppercase text-xs text-end">{{ __('Transactions Count') }}</th>
                                    <th class="px-6 py-3 uppercase text-xs text-end">{{ __('إجمالي المبالغ') }}</th>
                                    <th class="px-6 py-3 uppercase text-xs text-center">{{ __('الحالة') }}</th>
                                    <th class="px-6 py-3 uppercase text-xs text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="reportsTable" class="divide-y divide-gray-100 bg-white">
                                @forelse($ledgers as $ledger)
                                    <tr class="border-b hover:bg-gray-50 transition-colors">
                                        <td class="p-3 text-primary font-semibold">
                                            REP-{{ \Carbon\Carbon::parse($ledger->report_date)->format('ym') }}
                                        </td>
                                        <td class="p-3">
                                            {{ \Carbon\Carbon::parse($ledger->report_date)->translatedFormat('F Y') }}
                                        </td>
                                        <td class="p-3 text-center">
                                            {{ number_format($ledger->transactions_count) }}
                                        </td>
                                        <td class="p-3 font-bold text-left">
                                            ${{ number_format($ledger->recorded_volume ?? 0, 2) }}
                                        </td>
                                        <td class="p-3 text-center">
                                            <span
                                                class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">مكتمل</span>
                                        </td>
                                        <td class="p-3 text-center flex items-center justify-center gap-2">
                                            @php
                                                $monthStart = \Carbon\Carbon::parse($ledger->report_date)->startOfMonth()->format('Y-m-d');
                                                $monthEnd = \Carbon\Carbon::parse($ledger->report_date)->endOfMonth()->format('Y-m-d');
                                                $exportParams = array_merge(request()->except(['start_date', 'end_date', 'page']), [
                                                    'start_date' => $monthStart,
                                                    'end_date' => $monthEnd
                                                ]);
                                            @endphp
                                            <a href="{{ route('admin.reports.export.csv', $exportParams) }}"
                                                class="inline-flex items-center gap-1 text-green-700 bg-green-50 hover:bg-green-200 px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
                                                CSV
                                            </a>
                                            <a href="{{ route('admin.reports.export.pdf', $exportParams) }}"
                                                class="inline-flex items-center gap-1 text-red-700 bg-red-50 hover:bg-red-200 px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
                                                PDF
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-6 text-center text-gray-500">لا توجد سجلات مالية لعرضها في
                                            هذه الفترة.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(isset($ledgers) && $ledgers->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $ledgers->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script>
        window.financialChartData = @json($chartData ?? []);
    </script>
    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    <script src="{{ asset('modules/admin/js/reports.js') }}"></script>
</body>

</html>