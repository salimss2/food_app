<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Order Details') }} #{{ $order->id }} - {{ __('Admin Dashboard') }}</title>
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
        html[dir="rtl"] body {
            font-family: 'Cairo', sans-serif !important;
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

        /* Table alignment */
        html[dir="rtl"] table {
            text-align: right;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased overflow-hidden flex h-screen">

    @include('admin::layouts.partials.sidebar')

    <!-- Layout Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300">

        @include('admin::layouts.partials.header')

        <!-- Main Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto w-full">

            <!-- Breadcrumbs & Actions -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <div class="flex items-center space-x-2 text-xs text-gray-500 uppercase tracking-wider mb-1.5">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-primary transition-colors">{{ __('Dashboard') }}</a>
                        <span>/</span>
                        <a href="{{ route('admin.order-history.index') }}" class="hover:text-primary transition-colors">{{ __('Orders') }}</a>
                        <span>/</span>
                        <span class="text-gray-700 font-medium">{{ __('Order Details') }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-2xl font-bold text-gray-900">{{ __('Order Reference') }} #{{ $order->id }}</h2>
                        <span class="text-sm text-gray-400">|</span>
                        <p class="text-sm text-gray-500">
                            {{ $order->created_at->format('M d, Y - g:i A') }}
                        </p>
                    </div>
                </div>
                
                <div>
                    <a href="{{ route('admin.order-history.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary shadow-sm transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ __('Back to List') }}
                    </a>
                </div>
            </div>

            <!-- Main Order Details Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left Column: Details Cards -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Order Header / Status Banner -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-wrap justify-between items-center gap-4">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 bg-indigo-50 rounded-xl flex items-center justify-center text-primary">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ __('Status Details') }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ __('Payment Status:') }} 
                                    <span class="font-semibold text-gray-700 capitalize">
                                        {{ str_replace('_', ' ', $order->payment_status ?? 'N/A') }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <!-- Status Badges -->
                        <div class="flex items-center gap-2">
                            @php
                                $statusColors = [
                                    'pending_admin_approval' => 'bg-orange-100 text-orange-800 border-orange-200',
                                    'pending_driver_acceptance' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'driver_assigned' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'ready_for_pickup' => 'bg-purple-100 text-purple-800 border-purple-200',
                                    'on_the_way' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                    'delivered' => 'bg-green-100 text-green-800 border-green-200',
                                    'canceled' => 'bg-red-100 text-red-800 border-red-200',
                                ];
                                $statusLabels = [
                                    'pending_admin_approval' => __('Pending Admin Approval'),
                                    'pending_driver_acceptance' => __('Searching for Driver'),
                                    'driver_assigned' => __('Driver Assigned'),
                                    'ready_for_pickup' => __('Ready for Pickup'),
                                    'on_the_way' => __('On the Way'),
                                    'delivered' => __('Delivered'),
                                    'canceled' => __('Canceled'),
                                ];
                                $badgeClass = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                $badgeLabel = $statusLabels[$order->status] ?? str_replace('_', ' ', $order->status);
                            @endphp
                            <span class="px-4 py-2 border rounded-lg text-sm font-bold {{ $badgeClass }} uppercase tracking-wider shadow-sm">
                                {{ $badgeLabel }}
                            </span>
                        </div>
                    </div>

                    <!-- Items Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                            <h3 class="text-base font-bold text-gray-900">{{ __('Order Items') }}</h3>
                        </div>
                        <div class="overflow-x-auto w-full">
                            <table class="w-full whitespace-nowrap text-left text-sm text-gray-500">
                                <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">{{ __('Meal') }}</th>
                                        <th scope="col" class="px-6 py-3">{{ __('Special Instructions') }}</th>
                                        <th scope="col" class="px-6 py-3 text-center">{{ __('Quantity') }}</th>
                                        <th scope="col" class="px-6 py-3">{{ __('Unit Price') }}</th>
                                        <th scope="col" class="px-6 py-3 text-end">{{ __('Subtotal') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @forelse($order->items as $item)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 flex items-center">
                                                <img src="{{ $item->meal->image ?? 'https://ui-avatars.com/api/?name='.urlencode($item->name).'&color=7F9CF5&background=EBF4FF' }}" alt="{{ $item->name }}" class="w-12 h-12 object-cover rounded-lg border border-gray-200 me-3">
                                                <div>
                                                    <div class="text-sm font-bold text-gray-900">{{ $item->name }}</div>
                                                    @if($item->meal)
                                                        <div class="text-xs text-gray-500">{{ $item->meal->category->name ?? '' }}</div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="text-xs text-gray-600 italic">
                                                    {{ $item->special_instructions ?? __('None') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center font-semibold text-gray-900">
                                                {{ $item->quantity }}
                                            </td>
                                            <td class="px-6 py-4 font-medium text-gray-700">
                                                ${{ number_format($item->price, 2) }}
                                            </td>
                                            <td class="px-6 py-4 text-end font-bold text-gray-900">
                                                ${{ number_format($item->subtotal, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-8 text-center text-gray-400 italic">
                                                {{ __('No items found in this order.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Info Cards Grid -->
                <div class="space-y-6">

                    <!-- Customer Info Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 pb-2">
                            {{ __('Customer Details') }}
                        </h4>
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center text-primary font-bold text-sm uppercase">
                                {{ substr($order->user->name ?? 'U', 0, 2) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $order->user->name ?? __('Unknown Customer') }}</p>
                                <p class="text-xs text-gray-500">{{ $order->user->email ?? '' }}</p>
                            </div>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600 pt-2 border-t border-gray-50">
                            <p><span class="font-medium text-gray-800">{{ __('Phone:') }}</span> {{ $order->user->phone ?? __('Not provided') }}</p>
                            <p class="leading-relaxed"><span class="font-medium text-gray-800">{{ __('Address:') }}</span> {{ $order->address ?? __('Not provided') }}</p>
                        </div>
                    </div>

                    <!-- Restaurant Info Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 pb-2">
                            {{ __('Restaurant Details') }}
                        </h4>
                        @if($order->restaurant)
                            <div class="flex items-center gap-3">
                                <img src="{{ $order->restaurant->logo }}" alt="{{ $order->restaurant->name }}" class="h-10 w-10 object-cover rounded-lg border border-gray-200">
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $order->restaurant->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->restaurant->email ?? '' }}</p>
                                </div>
                            </div>
                            <div class="space-y-2 text-sm text-gray-600 pt-2 border-t border-gray-50">
                                <p><span class="font-medium text-gray-800">{{ __('Phone:') }}</span> {{ $order->restaurant->phone ?? __('Not provided') }}</p>
                                <p><span class="font-medium text-gray-800">{{ __('Address:') }}</span> {{ $order->restaurant->address ?? __('Not provided') }}</p>
                            </div>
                        @else
                            <p class="text-sm text-gray-400 italic">{{ __('Unknown Restaurant') }}</p>
                        @endif
                    </div>

                    <!-- Driver Info Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 pb-2">
                            {{ __('Driver Details') }}
                        </h4>
                        @if($order->driver)
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 bg-green-100 rounded-full flex items-center justify-center text-green-700 font-bold text-sm uppercase">
                                    {{ substr($order->driver->name, 0, 2) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $order->driver->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->driver->email ?? '' }}</p>
                                </div>
                            </div>
                            <div class="space-y-2 text-sm text-gray-600 pt-2 border-t border-gray-50">
                                <p><span class="font-medium text-gray-800">{{ __('Phone:') }}</span> {{ $order->driver->phone ?? __('Not provided') }}</p>
                                <p><span class="font-medium text-gray-800">{{ __('Status:') }}</span> 
                                    <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                        {{ __('Assigned') }}
                                    </span>
                                </p>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center py-4 text-center">
                                <div class="relative flex h-3 w-3 mb-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                                </div>
                                <p class="text-sm font-medium text-yellow-600">{{ __('Searching for Driver...') }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ __('No driver assigned yet.') }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Financial Summary Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 pb-2">
                            {{ __('Financial Summary') }}
                        </h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>{{ __('Subtotal') }}</span>
                                <span class="font-medium">${{ number_format($order->items->sum('subtotal'), 2) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>{{ __('Delivery Fee') }}</span>
                                <span class="font-medium">${{ number_format($order->delivery_fee, 2) }}</span>
                            </div>
                            @if($order->discount_amount > 0)
                                <div class="flex justify-between text-red-600 font-medium">
                                    <span>{{ __('Discount') }}</span>
                                    <span>-${{ number_format($order->discount_amount, 2) }}</span>
                                </div>
                            @endif
                            
                            <div class="pt-3 border-t border-gray-200 flex justify-between items-center">
                                <span class="text-base font-bold text-gray-900">{{ __('Grand Total') }}</span>
                                <span class="text-xl font-bold text-primary">${{ number_format($order->total, 2) }}</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </main>
    </div>

</body>

</html>
