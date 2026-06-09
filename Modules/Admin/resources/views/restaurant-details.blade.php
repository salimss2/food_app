<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Restaurant Details') }} - {{ __('Admin Dashboard') }}</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        .hidden-el {
            display: none !important;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4f46e5',
                        primary_dark: '#4338ca',
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

    @if (session("success"))
        <div id="success-toast"
            class="fixed top-5 left-1/2 -translate-x-1/2 z-[200] bg-green-600 text-white px-6 py-3 rounded-xl shadow-2xl flex items-center gap-3 animate-bounce">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="font-bold">{{ session("success") }}</span>
        </div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('success-toast');
                if (toast) {
                    toast.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                    setTimeout(() => toast.remove(), 500);
                }
            }, 3000);
        </script>
    @endif

    @include('admin::layouts.partials.sidebar')


    <!-- Layout Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300">

        @include('admin::layouts.partials.header')

        <!-- Main Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto w-full relative" data-id="123">

            <!-- Header Section (Top Card) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex flex-col lg:flex-row justify-between items-start gap-6">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 w-full lg:w-auto">
                        <div class="relative">
                            <img class="w-24 h-24 rounded-xl border-4 border-indigo-50 shadow-md object-cover"
                                src="{{ $restaurant->logo }}" alt="{{ $restaurant->name }}">
                            <div class="absolute -bottom-2 -right-2 {{ $restaurant->status === "open" ? "bg-green-500" : "bg-red-500" }} w-6 h-6 rounded-full border-2 border-white shadow-sm flex items-center justify-center"
                                title="{{ $restaurant->status === "open" ? __("Open Now") : __("Closed") }}">
                                <span class="w-2 h-2 rounded-full bg-white"></span>
                            </div>
                        </div>
                        <div class="text-center sm:text-start">
                            <h2 class="text-2xl font-bold text-gray-900">{{ $restaurant->name }}</h2>
                            <div class="flex items-center justify-center sm:justify-start gap-3 mt-1">
                                <div class="flex items-center text-yellow-400">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span
                                        class="ms-1 text-sm font-semibold text-gray-700">{{ $metrics['avg_rating'] }}</span>
                                </div>
                                <span class="text-sm text-gray-500">({{ $metrics['reviews_count'] }}
                                    {{ __("Reviews") }})</span>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">{{ $restaurant->category }}</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-2">{{ $restaurant->location }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-4 w-full lg:w-auto">
                        <!-- KPI Mini Cards in Header -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 w-full">
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">
                                    {{ __('Gross Sales') }}
                                </p>
                                <p class="text-lg font-bold text-gray-900">
                                    ${{ number_format($metrics['total_revenue'], 2) }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">
                                    {{ __('Net Profit') }}
                                </p>
                                <p class="text-lg font-bold text-green-600">
                                    ${{ number_format($metrics['net_profit'], 2) }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">{{ __('Orders') }}
                                </p>
                                <p class="text-lg font-bold text-gray-900">{{ $metrics['total_orders'] }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">
                                    {{ __('Top Item') }}
                                </p>
                                <p class="text-lg font-bold text-indigo-600">{{ $metrics['top_meal_name'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col xl:flex-row gap-6">
                <!-- Left Sidebar Panel (Mini Card) -->
                <div class="w-full xl:w-1/4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sticky top-20">
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b pb-2">
                            {{ __('Account Summary') }}
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider">{{ __('Activity Status') }}
                                </p>
                                <div class="mt-1 flex items-center">
                                    <div
                                        class="w-2 h-2 rounded-full {{ $restaurant->account_status === "Active" ? "bg-green-500" : "bg-red-500" }} me-2">
                                    </div>
                                    <span
                                        class="font-medium text-sm text-gray-900">{{ __($restaurant->account_status) }}</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider">{{ __('Store Status') }}</p>
                                <div class="mt-1 flex items-center">
                                    <div
                                        class="w-2 h-2 rounded-full {{ $restaurant->status === "open" ? "bg-blue-500" : "bg-gray-400" }} me-2">
                                    </div>
                                    <span
                                        class="font-medium text-sm text-gray-900">{{ $restaurant->status === "open" ? __("Open for Orders") : __("Closed") }}</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider">{{ __('Prep Time (Avg)') }}
                                </p>
                                <p class="mt-1 font-semibold text-lg text-gray-900">{{ __('18-22 mins') }}</p>
                            </div>


                        </div>
                    </div>
                </div>

                <!-- Main Content (Tabs System) -->
                <div class="w-full xl:w-3/4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">

                        <!-- Tabs Header -->
                        <div class="flex overflow-x-auto border-b border-gray-200 hide-scrollbar scroll-smooth">
                            <button onclick="switchTab('metrics')" id="tab-metrics"
                                class="tab-btn active shrink-0 px-6 py-4 text-sm font-medium border-b-2 border-transparent transition-all focus:outline-none">
                                {{ __('Performance Metrics') }}
                            </button>

                            <button onclick="switchTab('orders')" id="tab-orders"
                                class="tab-btn shrink-0 px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent transition-all focus:outline-none flex items-center">
                                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                {{ __('Orders') }}
                            </button>
                        </div>

                        <!-- Tabs Content -->
                        <div class="p-6">

                            <!-- Tab: Metrics -->
                            <div id="content-metrics" class="tab-content transition-opacity duration-300">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                    <div class="p-6 rounded-xl border border-gray-100 bg-gray-50">
                                        <h4 class="text-sm font-bold text-gray-600 uppercase mb-4">
                                            {{ __('Total Revenue Flow') }}
                                        </h4>
                                        <div class="h-48 flex items-end justify-between gap-2 px-2">
                                            @foreach($metrics['chart_data'] as $day => $revenue)
                                                <div class="w-full bg-indigo-600 rounded-t transition-all hover:bg-indigo-400"
                                                    style="height: {{ $metrics['max_revenue'] > 0 ? ($revenue / $metrics['max_revenue'] * 100) : 0 }}%"
                                                    title="{{ __($day) }}: ${{ number_format($revenue, 2) }}"></div>
                                            @endforeach
                                        </div>
                                        <div
                                            class="flex justify-between mt-2 text-[10px] uppercase font-bold text-gray-400">
                                            @foreach($metrics['chart_data'] as $day => $revenue)
                                                <span class="w-full text-center">{{ __($day) }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="p-6 rounded-xl border border-gray-100 bg-gray-50">
                                        <h4 class="text-sm font-bold text-gray-600 uppercase mb-4">
                                            {{ __('Order Status Mix') }}
                                        </h4>
                                        <div class="space-y-4 pt-2">
                                            <div class="relative pt-1">
                                                <div class="flex mb-2 items-center justify-between">
                                                    <div
                                                        class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-green-600 bg-green-200">
                                                        {{ __('Completed') }}
                                                    </div>
                                                    <div class="text-end"><span
                                                            class="text-xs font-semibold inline-block text-green-600">{{ $metrics['completed_percentage'] }}%</span>
                                                    </div>
                                                </div>
                                                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-green-200">
                                                    <div style="width:{{ $metrics['completed_percentage'] }}%"
                                                        class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="relative pt-1">
                                                <div class="flex mb-2 items-center justify-between">
                                                    <div
                                                        class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-red-600 bg-red-200">
                                                        {{ __('Cancelled') }}
                                                    </div>
                                                    <div class="text-end"><span
                                                            class="text-xs font-semibold inline-block text-red-600">{{ $metrics['cancelled_percentage'] }}%</span>
                                                    </div>
                                                </div>
                                                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-red-200">
                                                    <div style="width:{{ $metrics['cancelled_percentage'] }}%"
                                                        class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-red-500">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h4 class="text-sm font-bold text-gray-900 uppercase mb-4">{{ __('Top Selling Items') }}
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @foreach($metrics['top_meals'] as $index => $meal)
                                        <div
                                            class="flex items-center p-3 bg-white border border-gray-100 rounded-lg shadow-sm">
                                            <div
                                                class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600 font-bold me-4">
                                                #{{ $index + 1 }}</div>
                                            <div>
                                                <p class="font-bold text-gray-900">{{ $meal['name'] }}</p>
                                                <p class="text-xs text-gray-500">{{ $meal['qty'] }} {{ __('units sold') }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>



                            <!-- Tab: Menu Preview -->
                            <div id="content-menu" class="tab-content hidden-el transition-opacity duration-300">
                                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                                    <div
                                        class="flex flex-wrap items-center gap-4 w-full justify-between sm:justify-start">
                                        <div class="inline-flex space-x-reverse space-x-2">
                                            <button onclick="filterMeals('all')"
                                                class="bg-gray-100 text-gray-900 px-3 py-1.5 rounded-full text-xs font-bold ring-1 ring-gray-200">{{ __('All Items') }}</button>
                                            @foreach($restaurant->mealCategories as $category)
                                                <button onclick="filterMeals({{ $category->id }})"
                                                    class="text-gray-500 hover:text-gray-900 px-3 py-1.5 text-xs font-bold transition-colors flex items-center">
                                                    <img src="{{ $category->image ?? 'https://ui-avatars.com/api/?name=' . urlencode($category->name) . '&color=7F9CF5&background=EBF4FF' }}"
                                                        class="w-6 h-6 rounded-full object-cover inline-block ml-2"
                                                        alt="{{ $category->name }}">
                                                    {{ $category->name }}
                                                </button>
                                            @endforeach
                                        </div>

                                        <div class="flex gap-2">
                                            {{-- Add New Category Button --}}
                                            <button onclick="openModal('addCategoryModal')"
                                                class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center transition shadow-sm">
                                                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                {{ __('إضافة فئة جديدة') }}
                                            </button>

                                            {{-- Add New Meal Button --}}
                                            <button onclick="openModal('addMealModal')"
                                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center transition shadow-sm">
                                                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                {{ __('إضافة وجبة جديدة') }}
                                            </button>
                                        </div>
                                    </div>
                                    <div class="relative w-full sm:w-64">
                                        <input type="text" placeholder="{{ __('Search menu...') }}"
                                            class="w-full ps-9 pe-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all">
                                        <svg class="w-4 h-4 text-gray-400 absolute start-3 top-2.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    @forelse($restaurant->meals as $meal)
                                        <div class="meal-card group bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow relative"
                                            data-category-id="{{ $meal->meal_category_id }}">
                                            <div class="relative">
                                                <img src="{{ $meal->image }}" class="w-full h-32 object-cover"
                                                    alt="{{ $meal->name }}"
                                                    onerror="this.src='https://images.unsplash.com/photo-1513104890138-7c749659a591?w=400&h=200&fit=crop'">

                                                {{-- Action Buttons --}}
                                                <div
                                                    class="absolute top-2 left-2 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <button
                                                        onclick="openEditMealModal('{{ base64_encode($meal->toJson()) }}')"
                                                        class="bg-white/90 backdrop-blur p-1.5 rounded-lg shadow-sm hover:bg-indigo-50 text-indigo-600 transition"
                                                        title="{{ __('Edit') }}">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                    <form action="{{ route('admin.meals.destroy', $meal->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('{{ __('هل أنت متأكد من حذف هذه الوجبة نهائياً؟') }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="bg-white/90 backdrop-blur p-1.5 rounded-lg shadow-sm hover:bg-red-50 text-red-600 transition"
                                                            title="{{ __('Delete') }}">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="p-4">
                                                <div class="flex justify-between items-start mb-2 text-balance">
                                                    <h5 class="font-bold text-gray-900">{{ $meal->name }}</h5>
                                                    <span
                                                        class="text-indigo-600 font-bold">${{ number_format($meal->price, 2) }}</span>
                                                </div>
                                                <p class="text-xs text-gray-500 line-clamp-2 mb-4">
                                                    {{ $meal->description }}
                                                </p>
                                                <div class="flex justify-between items-center border-t border-gray-50 pt-3">
                                                    <span id="status-text-{{ $meal->id }}"
                                                        class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $meal->available ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                                                        {{ $meal->available ? __('Available') : __('Unavailable') }}
                                                    </span>
                                                    <button id="toggle-btn-{{ $meal->id }}"
                                                        onclick="toggleMealAvailability({{ $meal->id }})"
                                                        class="text-[10px] font-bold uppercase hover:underline {{ $meal->available ? 'text-red-600' : 'text-green-600' }}">
                                                        {{ $meal->available ? __('Disable Item') : __('Enable Item') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-span-full py-12 text-center text-gray-500">
                                            {{ __("No meals found for this restaurant.") }}
                                        </div>
                                    @endforelse
                                  </div>
                            </div>

                            <!-- Tab: Orders -->
                            <div id="content-orders" class="tab-content hidden-el transition-opacity duration-300">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-separate border-spacing-y-2">
                                        <thead>
                                            <tr class="text-gray-400 text-xs uppercase tracking-widest">
                                                <th class="px-4 py-3 font-bold">{{ __('Order ID') }}</th>
                                                <th class="px-4 py-3 font-bold">{{ __('Customer') }}</th>
                                                <th class="px-4 py-3 font-bold">{{ __('Total') }}</th>
                                                <th class="px-4 py-3 font-bold">{{ __('Status') }}</th>
                                                <th class="px-4 py-3 font-bold">{{ __('Date') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($latestOrders as $order)
                                                <tr class="bg-gray-50/50 hover:bg-gray-100/50 transition-colors rounded-xl">
                                                    <td class="px-4 py-4 text-sm font-bold text-gray-900">
                                                        #{{ $order->order_number ?? $order->id }}</td>
                                                    <td class="px-4 py-4 text-sm text-gray-600">
                                                        {{ $order->user->name ?? 'N/A' }}
                                                    </td>
                                                    <td class="px-4 py-4 text-sm font-bold text-indigo-600">
                                                        ${{ number_format($order->total, 2) }}</td>
                                                    <td class="px-4 py-4">
                                                        @php
                                                            $statusColors = [
                                                                'delivered' => 'bg-green-100 text-green-700',
                                                                'canceled' => 'bg-red-100 text-red-700',
                                                                'pending_admin_approval' => 'bg-yellow-100 text-yellow-700',
                                                                'pending_driver_acceptance' => 'bg-blue-100 text-blue-700',
                                                            ];
                                                            $colorClass = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-700';
                                                        @endphp
                                                        <span
                                                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $colorClass }}">
                                                            {{ __($order->status) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-4 text-sm text-gray-500">
                                                        {{ $order->created_at->format('Y-m-d H:i') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if($latestOrders->isEmpty())
                                        <div class="text-center py-10 text-gray-500">
                                            {{ __('No orders found for this restaurant.') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modals -->
    <!-- Add New Category Modal -->
    <div id="addCategoryModal" class="relative z-50 hidden-el" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-content">
                    <div
                        class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">{{ __('إضافة فئة جديدة') }}</h3>
                        <button onclick="closeModal('addCategoryModal')" class="text-gray-400 hover:text-gray-600"><svg
                                class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg></button>
                    </div>
                    <form action="{{ route('admin.restaurants.store-category') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
                        <div class="p-6 space-y-4">
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('اسم الفئة') }}</label>
                                <input type="text" name="name" required
                                    class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('صورة الفئة') }}</label>
                                <input type="file" name="image"
                                    class="w-full text-sm text-gray-500 file:me-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex gap-3">
                            <button type="submit"
                                class="flex-1 bg-emerald-600 text-white py-2 rounded-lg font-bold text-sm hover:bg-emerald-700 transition">{{ __('حفظ') }}</button>
                            <button type="button" onclick="closeModal('addCategoryModal')"
                                class="flex-1 bg-white border border-gray-200 text-gray-600 py-2 rounded-lg font-bold text-sm hover:bg-gray-50 transition">{{ __('إلغاء') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Meal Modal -->
    <div id="editMealModal" class="relative z-50 hidden-el" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-content">
                    <div
                        class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">{{ __('تعديل الوجبة') }}</h3>
                        <button onclick="closeModal('editMealModal')" class="text-gray-400 hover:text-gray-600"><svg
                                class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg></button>
                    </div>
                    <form id="editMealForm" action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="p-6 space-y-4">
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('اسم الوجبة') }}</label>
                                <input type="text" name="name" id="edit_meal_name" required
                                    class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('فئة الوجبة') }}</label>
                                <select name="category_id" id="edit_meal_category_id" required
                                    class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none">
                                    <option value="" disabled>{{ __('اختر الفئة...') }}</option>
                                    @foreach($restaurant->mealCategories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('سعر الوجبة') }}</label>
                                <input type="number" step="0.01" name="price" id="edit_meal_price" required
                                    class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('وصف الوجبة') }}</label>
                                <textarea name="description" id="edit_meal_description" rows="3"
                                    class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none"></textarea>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('صورة الوجبة') }}</label>
                                <input type="file" name="image"
                                    class="w-full text-sm text-gray-500 file:me-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex gap-3">
                            <button type="submit"
                                class="flex-1 bg-primary text-white py-2 rounded-lg font-bold text-sm hover:bg-primary_dark transition">{{ __('حفظ التعديلات') }}</button>
                            <button type="button" onclick="closeModal('editMealModal')"
                                class="flex-1 bg-white border border-gray-200 text-gray-600 py-2 rounded-lg font-bold text-sm hover:bg-gray-50 transition">{{ __('إلغاء') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="addMealModal" class="relative z-50 hidden-el" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-content">
                    <div
                        class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">{{ __('إضافة وجبة جديدة') }}</h3>
                        <button onclick="closeModal('addMealModal')" class="text-gray-400 hover:text-gray-600"><svg
                                class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg></button>
                    </div>
                    <form action="{{ route('admin.restaurants.store-meal') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
                        <div class="p-6 space-y-4">
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('اسم الوجبة') }}</label>
                                <input type="text" name="name" required
                                    class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('فئة الوجبة') }}</label>
                                <select name="category_id" required
                                    class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none">
                                    <option value="" disabled selected>{{ __('اختر الفئة...') }}</option>
                                    @foreach($restaurant->mealCategories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('سعر الوجبة') }}</label>
                                <input type="number" step="0.01" name="price" required
                                    class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('وصف الوجبة') }}</label>
                                <textarea name="description" rows="3"
                                    class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none"></textarea>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('صورة الوجبة') }}</label>
                                <input type="file" name="image"
                                    class="w-full text-sm text-gray-500 file:me-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex gap-3">
                            <button type="submit"
                                class="flex-1 bg-primary text-white py-2 rounded-lg font-bold text-sm hover:bg-primary_dark transition">{{ __('حفظ') }}</button>
                            <button type="button" onclick="closeModal('addMealModal')"
                                class="flex-1 bg-white border border-gray-200 text-gray-600 py-2 rounded-lg font-bold text-sm hover:bg-gray-50 transition">{{ __('إلغاء') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Notification Modal -->
    <div id="notificationModal" class="relative z-50 hidden-el" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-content">
                    <div
                        class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">{{ __('Push Notification') }}</h3>
                        <button onclick="closeModal('notificationModal')" class="text-gray-400 hover:text-gray-600"><svg
                                class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg></button>
                    </div>
                    <div class="p-6">
                        <label
                            class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('Message Subject') }}</label>
                        <input type="text"
                            class="w-full border border-gray-200 rounded-lg p-2.5 text-sm mb-4 focus:ring-1 focus:ring-primary focus:border-primary outline-none"
                            placeholder="{{ __('e.g. Schedule Maintenance Notice') }}">
                        <label
                            class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('Detailed Content') }}</label>
                        <textarea rows="4"
                            class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none"
                            placeholder="{{ __('Enter notification message here...') }}"></textarea>
                        <div class="mt-6 flex gap-3">
                            <button onclick="handleAction('Notification Sent')"
                                class="flex-1 bg-primary text-white py-2 rounded-lg font-bold text-sm hover:bg-primary_dark transition">{{ __('Send Now') }}</button>
                            <button onclick="closeModal('notificationModal')"
                                class="flex-1 bg-gray-100 text-gray-600 py-2 rounded-lg font-bold text-sm hover:bg-gray-200 transition">{{ __('Cancel') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Commission Modal -->
    <div id="commissionModal" class="relative z-50 hidden-el" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md modal-content">
                    <div class="p-6 text-center">
                        <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Adjust Commission') }}</h3>
                        <p class="text-sm text-gray-500 mb-6">
                            {{ __('Set a custom commission percentage for this restaurant.') }}
                        </p>
                        <div class="relative max-w-[120px] mx-auto">
                            <input type="number" value="12"
                                class="w-full border-2 border-primary rounded-xl p-3 text-center text-2xl font-bold bg-indigo-50 outline-none text-indigo-700">
                            <span class="absolute end-4 top-4 text-indigo-400 font-bold">%</span>
                        </div>
                        <div class="mt-8 flex gap-3">
                            <button onclick="handleAction('Commission Updated')"
                                class="flex-1 bg-primary text-white py-2.5 rounded-lg font-bold text-sm shadow-md">{{ __('Apply Change') }}</button>
                            <button onclick="closeModal('commissionModal')"
                                class="flex-1 bg-white border border-gray-200 text-gray-600 py-2.5 rounded-lg font-bold text-sm">{{ __('Discard') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="relative z-[100] hidden-el">
        <div class="fixed inset-0 bg-black/90 backdrop-blur-sm modal-overlay" onclick="closeModal('imageModal')"></div>
        <div class="fixed inset-0 z-10 flex items-center justify-center p-4" onclick="closeModal('imageModal')">
            <img id="enlargedImg" src=""
                class="max-w-[90vw] max-h-[90vh] object-contain rounded shadow-2xl modal-content">
        </div>
    </div>

    <div id="toast-container"></div>

    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    <script src="{{ asset('modules/admin/js/restaurant-details.js') }}"></script>
    <script>
        function filterMeals(categoryId) {
            const cards = document.querySelectorAll('.meal-card');
            cards.forEach(card => {
                if (categoryId === 'all' || card.dataset.categoryId == categoryId) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        }
        function openEditMealModal(base64Meal) {
            try {
                const meal = JSON.parse(atob(base64Meal));
                const form = document.getElementById('editMealForm');

                // Set form action
                form.action = `/admin/meals/${meal.id}`;

                // Populate fields
                document.getElementById('edit_meal_name').value = meal.name;
                document.getElementById('edit_meal_category_id').value = meal.meal_category_id;
                document.getElementById('edit_meal_price').value = meal.price;
                document.getElementById('edit_meal_description').value = meal.description || '';

                openModal('editMealModal');
            } catch (e) {
                console.error('Error opening edit modal:', e);
            }
        }
        function toggleMealAvailability(mealId) {
            fetch(`/admin/meals/${mealId}/toggle-availability`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        const statusText = document.getElementById(`status-text-${mealId}`);
                        const toggleBtn = document.getElementById(`toggle-btn-${mealId}`);

                        if (data.available) {
                            statusText.innerText = '{{ __("Available") }}';
                            statusText.className = 'px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-green-50 text-green-600';
                            toggleBtn.innerText = '{{ __("Disable Item") }}';
                            toggleBtn.className = 'text-[10px] font-bold uppercase hover:underline text-red-600';
                        } else {
                            statusText.innerText = '{{ __("Unavailable") }}';
                            statusText.className = 'px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-red-50 text-red-600';
                            toggleBtn.innerText = '{{ __("Enable Item") }}';
                            toggleBtn.className = 'text-[10px] font-bold uppercase hover:underline text-green-600';
                        }

                        // Show toast
                        showToast(data.message);
                    }
                })
                .catch(error => console.error('Error toggling availability:', error));
        }

        function showToast(message) {
            const toast = document.createElement('div');
            toast.className = 'fixed top-5 left-1/2 -translate-x-1/2 z-[200] bg-green-600 text-white px-6 py-3 rounded-xl shadow-2xl flex items-center gap-3 animate-bounce';
            toast.innerHTML = `
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-bold">${message}</span>
            `;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        }

        // Map Initialization
        var map;
        var drivers = @json($drivers);
        var resLat = {{ $restaurant->latitude ?? 14.5425 }};
        var resLng = {{ $restaurant->longitude ?? 49.1242 }};

        document.addEventListener('DOMContentLoaded', function () {
            map = L.map('driversMap').setView([resLat, resLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            // Restaurant Marker
            L.marker([resLat, resLng], {
                icon: L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                })
            }).addTo(map).bindPopup("{{ $restaurant->name }}").openPopup();

            // Driver Markers
            drivers.forEach(function (driver) {
                if (driver.lat && driver.lng) {
                    L.marker([driver.lat, driver.lng]).addTo(map)
                        .bindPopup("{{ __('Driver') }}: " + driver.name);
                }
            });

            // Handle Map Resize on Tab Switch
            const baseSwitchTab = window.switchTab;
            window.switchTab = function (tabId) {
                if (typeof baseSwitchTab === 'function') baseSwitchTab(tabId);
                if (tabId === 'logistics' && map) {
                    setTimeout(() => { map.invalidateSize(); }, 300);
                }
            };
        });
    </script>
</body>

</html>