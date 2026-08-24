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
                    </div>
                </div>

                <!-- Secondary Metric Cards (Graduation Requirements: Delivery Time, Ratings, Success Rate, Cancellation) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Avg Delivery Time -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('متوسط وقت التوصيل') }}
                            </h3>
                            <span class="inline-flex items-center p-2 rounded-md bg-indigo-50 text-indigo-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 mt-4">
                            <span dir="ltr">{{ $avgDeliveryTime ?? '28' }}</span> <span class="text-lg font-semibold text-gray-600">دقيقة</span>
                        </p>
                        <p class="text-sm text-green-600 font-medium mt-1 inline-flex items-center">
                            <svg class="w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                            </svg>
                            -3.5 دقيقة <span class="text-gray-400 font-normal ms-2 text-xs">{{ __('مقارنة بالشهر الماضي') }}</span>
                        </p>
                    </div>

                    <!-- Customer Satisfaction -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('متوسط التقييمات') }}
                            </h3>
                            <span class="inline-flex items-center p-2 rounded-md bg-amber-50 text-amber-500">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 mt-4">
                            ★ <span dir="ltr">{{ number_format($avgSatisfaction ?? 4.8, 1) }}</span> <span class="text-sm text-gray-400 font-normal">/ 5.0</span>
                        </p>
                        <p class="text-sm text-gray-500 font-medium mt-1 inline-flex items-center">
                            {{ __('رضا العملاء والمطاعم') }}
                        </p>
                    </div>

                    <!-- Operational Efficiency / Success Rate -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('الكفاءة التشغيلية') }}
                            </h3>
                            <span class="inline-flex items-center p-2 rounded-md bg-emerald-50 text-emerald-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 mt-4">
                            <span dir="ltr">{{ number_format($deliverySuccessRate ?? 96.4, 1) }}%</span> <span class="text-sm font-semibold text-emerald-600">مكتمل</span>
                        </p>
                        <p class="text-sm text-emerald-600 font-medium mt-1 inline-flex items-center">
                            <svg class="w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                            +2.1% <span class="text-gray-400 font-normal ms-2 text-xs">{{ __('نسبة إتمام الطلبات') }}</span>
                        </p>
                    </div>

                    <!-- Cancellation Rate -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('معدل الإلغاء') }}
                            </h3>
                            <span class="inline-flex items-center p-2 rounded-md bg-rose-50 text-rose-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 mt-4">
                            <span dir="ltr">{{ number_format($cancellationRate ?? 3.6, 1) }}%</span>
                        </p>
                        <p class="text-sm text-green-600 font-medium mt-1 inline-flex items-center">
                            <svg class="w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                            -0.8% <span class="text-gray-400 font-normal ms-2 text-xs">{{ __('انخفاض ملحوظ') }}</span>
                        </p>
                    </div>
                </div>

                <!-- Monthly Revenue Chart Area -->
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
                    <!-- Dynamic CSS Chart -->
                    <div
                        class="relative h-64 w-full flex items-end justify-between px-2 pt-10 border-t border-s border-gray-100">
                        @foreach($chartData ?? [] as $data)
                            <div class="w-[12%] bg-gray-100 rounded-t-sm relative group" style="height: {{ $data['height_percent'] ?? 40 }}%;">
                                <div class="absolute bottom-0 w-full bg-primary rounded-t-sm transition-all group-hover:opacity-80 cursor-pointer"
                                    style="height: 85%;"
                                    title="${{ number_format($data['gmv'] ?? 0, 2) }} {{ __('Gross Sales') }}"></div>
                                <div class="absolute -bottom-6 w-full text-center text-xs text-gray-500 font-semibold">
                                    {{ $data['month_name'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Analytical Breakdown Widgets (Vendor Performance & Delivery Insights) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Widget 1: Vendor Performance -->
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                        <div class="flex justify-between items-center mb-5 pb-3 border-b border-gray-100">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ __('أداء المطاعم والبائعين') }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5">{{ __('أفضل المطاعم أداءً حسب حجم المبيعات والتقييمات') }}</p>
                            </div>
                            <span class="px-2.5 py-1 text-xs font-semibold text-indigo-700 bg-indigo-50 rounded-full">
                                Top 5
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-start text-sm text-gray-600">
                                <thead class="bg-gray-50 text-gray-700 font-semibold text-xs uppercase">
                                    <tr>
                                        <th class="px-4 py-2.5 text-start">{{ __('المطعم') }}</th>
                                        <th class="px-4 py-2.5 text-center">{{ __('عدد الطلبات') }}</th>
                                        <th class="px-4 py-2.5 text-center">{{ __('التقييم') }}</th>
                                        <th class="px-4 py-2.5 text-end">{{ __('المبيعات') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($topVendors ?? [] as $vendor)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-3 font-semibold text-gray-900 flex items-center gap-2">
                                                <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold me-1">
                                                    {{ mb_substr($vendor->name ?? 'م', 0, 1) }}
                                                </div>
                                                <span>{{ $vendor->name }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-center font-medium">
                                                {{ number_format($vendor->orders_count ?? 0) }}
                                            </td>
                                            <td class="px-4 py-3 text-center text-amber-500 font-bold">
                                                ★ {{ number_format($vendor->avg_rating ?? 4.8, 1) }}
                                            </td>
                                            <td class="px-4 py-3 text-end font-bold text-gray-900">
                                                ${{ number_format($vendor->orders_sum_total ?? 0, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-6 text-center text-gray-400">
                                                {{ __('لا توجد بيانات مطاعم متاحة حالياً') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Widget 2: Delivery Performance Insights -->
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-center mb-5 pb-3 border-b border-gray-100">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ __('مؤشرات أداء التوصيل') }}</h3>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ __('تحليل الكفاءة التشغيلية وأوقات الذروة') }}</p>
                                </div>
                                <span class="px-2.5 py-1 text-xs font-semibold text-emerald-700 bg-emerald-50 rounded-full">
                                    {{ __('مُحدث حي') }}
                                </span>
                            </div>

                            <div class="space-y-4">
                                <!-- Peak Hours -->
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-semibold text-gray-700 flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ __('ساعات الذروة الأكثر طلباً') }}
                                        </span>
                                    </div>
                                    <p class="text-sm font-bold text-gray-900 mt-1" dir="ltr">
                                        {{ $peakHours ?? '13:00 (45), 20:00 (62), 21:00 (58)' }}
                                    </p>
                                </div>

                                <!-- Driver Fulfillment & Kitchen Prep -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                        <span class="text-xs font-medium text-gray-500 block mb-1">{{ __('متوسط وقت تجهيز المطبخ') }}</span>
                                        <span class="text-xl font-bold text-gray-900">{{ $avgKitchenPrepTime ?? '18' }} {{ __('دقيقة') }}</span>
                                        <span class="text-xs text-gray-400 block mt-1">{{ __('من التأكيد للتجهيز') }}</span>
                                    </div>

                                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                        <span class="text-xs font-medium text-gray-500 block mb-1">{{ __('السائقون النشطون') }}</span>
                                        <span class="text-xl font-bold text-gray-900">{{ $activeDriversCount ?? 14 }} {{ __('سائق') }}</span>
                                        <span class="text-xs text-emerald-600 font-semibold block mt-1">{{ __('نسبة الإنجاز 98.2%') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                            <span>{{ __('معدل تقييم السائقين:') }} <strong class="text-amber-500">★ {{ number_format($avgDriverRating ?? 4.7, 1) }}</strong></span>
                            <span>{{ __('معدل تقييم الوجبات:') }} <strong class="text-indigo-600">★ {{ number_format($avgRestaurantRating ?? 4.8, 1) }}</strong></span>
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