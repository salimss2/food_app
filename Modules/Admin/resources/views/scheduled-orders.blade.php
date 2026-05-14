<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Scheduled Orders') }} - {{ __('Admin Dashboard') }}</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
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
        html[dir="rtl"] body { font-family: 'Cairo', sans-serif !important; }
        html[dir="rtl"] aside { left: auto !important; right: 0 !important; border-right: none !important; border-left: 1px solid #e5e7eb !important; }
        html[dir="rtl"] .space-x-4 > :not([hidden]) ~ :not([hidden]) { --tw-space-x-reverse: 1 !important; }
        html[dir="rtl"] .space-x-2 > :not([hidden]) ~ :not([hidden]) { --tw-space-x-reverse: 1 !important; }
        html[dir="rtl"] #langDropdownMenu, html[dir="rtl"] #profileDropdownMenu { right: auto !important; left: 0 !important; }
    </style></head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased overflow-hidden flex h-screen">

    @include('admin::layouts.partials.sidebar')










    <!-- Layout Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300">
        
        @include('admin::layouts.partials.header')

        <!-- Main Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto w-full">
            
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ __('Scheduled Orders') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ __('Orders set to be delivered at a specific future date and time.') }}</p>
                </div>
            </div>

            <!-- Grouped List -->
            <div id="scheduledGroupsContainer" class="space-y-8">
                @forelse($groupedOrders as $date => $orders)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider relative ps-4">
                                <span class="absolute start-0 top-1/2 -mt-1.5 w-3 h-3 bg-indigo-500 rounded-full"></span>
                                {{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}
                            </h3>
                            <span class="text-xs font-semibold bg-indigo-100 text-indigo-700 px-2.5 py-1 rounded-full">
                                {{ $orders->count() }} {{ __('Orders') }}
                            </span>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @foreach($orders as $order)
                                @php
                                    $diffInMinutes = \Carbon\Carbon::now()->diffInMinutes(\Carbon\Carbon::parse($order->scheduled_at), false);
                                    $nearingDispatch = ($diffInMinutes > 0 && $diffInMinutes <= 60);
                                @endphp
                                <div class="p-6 hover:bg-gray-50 transition-colors flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0 {{ $nearingDispatch ? 'bg-orange-50/50' : '' }}">
                                    <!-- Left Section -->
                                    <div class="flex items-center space-x-4 w-full sm:w-1/3">
                                        <div class="flex-shrink-0 bg-indigo-50 p-2 rounded-lg text-indigo-600 border border-indigo-100 {{ $nearingDispatch ? 'text-orange-600 bg-orange-100 border-orange-200 animate-pulse' : '' }}">
                                            <p class="text-xs font-semibold text-center leading-tight">
                                                <span class="block text-[10px] text-gray-500 uppercase">{{ __('Time') }}</span>
                                                {{ \Carbon\Carbon::parse($order->scheduled_at)->format('g:i A') }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 border-b border-gray-100 pb-0.5 inline-block">
                                                #{{ $order->id }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1 flex items-center">
                                                <svg class="h-3.5 w-3.5 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                {{ $order->user->name ?? __('Unknown Customer') }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Middle Section (Restaurant & Status) -->
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between w-full sm:w-1/2 space-y-3 sm:space-y-0">
                                        <div class="w-full sm:w-1/2">
                                            <span class="text-[10px] uppercase font-bold text-gray-400 block mb-0.5">{{ __('Pickup Location') }}</span>
                                            <p class="text-sm font-medium text-gray-800">{{ $order->restaurant->name ?? __('Unknown Restaurant') }}</p>
                                        </div>
                                        <div class="w-full sm:w-1/2 text-start sm:text-end">
                                            @if($nearingDispatch)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-orange-100 text-orange-800 border border-orange-200">
                                                    {{ __('Dispatch in') }} {{ $diffInMinutes }}m
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 flex flex-col items-center justify-center text-center">
                        <svg class="h-12 w-12 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('No scheduled orders') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('There are no orders set for a future delivery time.') }}</p>
                    </div>
                @endforelse
            </div>

        </main>
    </div>

    <!-- Order Details Modal (Reused) -->
    <div id="orderDetailsModal" class="relative z-40 hidden-el" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-start shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl modal-content">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2" id="modalOrderId">{{ __('Order Details') }}</h3>
                        <button onclick="closeModal('orderDetailsModal')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    
                    <div class="px-6 py-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ __('Customer Info') }}</h4>
                                    <div class="flex items-center space-x-3 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold" id="detailCustomerInitials">
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900" id="detailCustomerName">-</p>
                                            <p class="text-xs text-gray-500" id="detailCustomerPhone">-</p>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-600">
                                        <span class="font-medium text-gray-800">{{ __('Delivery Address:') }}</span>
                                        <p class="mt-1" id="detailAddress">-</p>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ __('Restaurant & Driver') }}</h4>
                                    <div class="space-y-2 text-sm">
                                        <p><span class="font-medium text-gray-800">Restaurant:</span> <span id="detailRestaurant">-</span></p>
                                        <p><span class="font-medium text-gray-800">{{ __('Driver Assigned:') }}</span> <span id="detailDriver">{{ __('Scheduled') }}</span></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 text-indigo-600">{{ __('Scheduled Time') }}</h4>
                                <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-3 mb-4 flex justify-between items-center text-indigo-900 font-semibold shadow-sm" id="detailScheduleBadge">
                                    -
                                </div>

                                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 mt-6">{{ __('Order Items') }}</h4>
                                <div class="bg-gray-50 rounded-lg border border-gray-100 overflow-hidden">
                                    <ul class="divide-y divide-gray-200" id="detailItemsList">
                                    </ul>
                                    <div class="bg-gray-100 px-4 py-3 border-t border-gray-200 flex justify-between items-center sm:px-6">
                                        <span class="text-sm font-semibold text-gray-900">{{ __('Total Price') }}</span>
                                        <span class="text-lg font-bold text-gray-900" id="detailTotal">$0.00</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
</body>
</html>
