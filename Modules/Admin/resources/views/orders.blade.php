<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Active Orders') }} - {{ __('Admin Dashboard') }}</title>
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
        /* Search icon position flip */
        html[dir="rtl"] .search-icon-wrapper { left: auto !important; right: 0 !important; }
        html[dir="rtl"] #customerNameInput { padding-left: 0.75rem !important; padding-right: 2.25rem !important; }
        /* Select arrow flip */
        html[dir="rtl"] select { background-position: left 0.5rem center !important; padding-left: 2rem !important; padding-right: 0.75rem !important; }
        /* Table text alignment */
        html[dir="rtl"] table { text-align: right; }
        html[dir="rtl"] .actions-cell { text-align: left !important; }
    </style></head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased overflow-hidden flex h-screen">

    @include('admin::layouts.partials.sidebar')










    <!-- Layout Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300">

        @include('admin::layouts.partials.header')

        <!-- Main Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto w-full">

            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900">{{ __('Active Orders') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('Live tracking of ongoing deliveries from restaurants to customers.') }}
                </p>
            </div>

            <!-- Global KPIs -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- KPI 1 -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                    <div class="p-3 rounded-full bg-green-50 text-green-600 me-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __("Today's Sales") }}</p>
                        <p class="text-2xl font-bold text-gray-900">${{ number_format($salesToday, 2) }}</p>
                    </div>
                </div>
                <!-- KPI 2 -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                    <div class="p-3 rounded-full bg-blue-50 text-blue-600 me-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('Active Orders') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $activeOrdersCount }}</p>
                    </div>
                </div>
                <!-- KPI 3 -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                    <div class="p-3 rounded-full bg-indigo-50 text-indigo-600 me-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('Fleet Available') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $fleetAvailableCount }} <span
                                class="text-sm text-gray-400 font-normal">{{ __('Drivers') }}</span></p>
                    </div>
                </div>
                <!-- KPI 4 -->
                <div class="bg-white rounded-xl shadow-sm border border-red-100 p-6 flex items-center">
                    <div class="p-3 rounded-full bg-red-50 text-red-600 me-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-red-500">{{ __('Delayed Orders') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $delayedOrdersCount }}</p>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                <!-- Unified Live-Search Toolbar -->
                <div class="p-4 border-b border-gray-200">
                    <form id="ordersFilterForm" method="GET" action="{{ route('admin.orders.index') }}"
                        class="flex flex-wrap items-center gap-2">

                        {{-- 1. Customer Name (live search with debounce) --}}
                        <div class="relative">
                            <div class="search-icon-wrapper absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" id="customerNameInput" name="customer_name"
                                value="{{ request('customer_name') }}" placeholder="{{ __('Search by customer name...') }}"
                                class="h-9 w-52 ps-9 pe-3 rounded-md border border-gray-300 bg-white text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm">
                        </div>

                        {{-- 2. Status --}}
                        <select name="status"
                            class="h-9 rounded-md border border-gray-300 bg-white ps-3 pe-8 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                            onchange="this.form.submit()">
                            <option value="All" {{ request('status', 'All') === 'All' ? 'selected' : '' }}>{{ __('All Statuses') }}
                            </option>
                            <option value="pending_admin_approval" {{ request('status') === 'pending_admin_approval' ? 'selected' : '' }}>{{ __('Pending Admin') }}</option>
                            <option value="pending_driver_acceptance" {{ request('status') === 'pending_driver_acceptance' ? 'selected' : '' }}>{{ __('Searching for Driver') }}</option>
                            <option value="driver_assigned" {{ request('status') === 'driver_assigned' ? 'selected' : '' }}>{{ __('Driver Assigned') }}</option>
                            <option value="ready_for_pickup" {{ request('status') === 'ready_for_pickup' ? 'selected' : '' }}>{{ __('Ready for Pickup') }}</option>
                            <option value="on_the_way" {{ request('status') === 'on_the_way' ? 'selected' : '' }}>{{ __('On the Way') }}</option>
                        </select>

                        {{-- 3. Restaurant --}}
                        <select name="restaurant_id"
                            class="h-9 rounded-md border border-gray-300 bg-white ps-3 pe-8 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                            onchange="this.form.submit()">
                            <option value="">{{ __('All Restaurants') }}</option>
                            @foreach($restaurants as $restaurant)
                                <option value="{{ $restaurant->id }}" {{ request('restaurant_id') == $restaurant->id ? 'selected' : '' }}>{{ $restaurant->name }}</option>
                            @endforeach
                        </select>

                        {{-- 4. Driver --}}
                        <select name="driver_id"
                            class="h-9 rounded-md border border-gray-300 bg-white ps-3 pe-8 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                            onchange="this.form.submit()">
                            <option value="">{{ __('All Drivers') }}</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}" {{ request('driver_id') == $driver->id ? 'selected' : '' }}>
                                    {{ $driver->name }}</option>
                            @endforeach
                        </select>

                        {{-- 5. Payment Method --}}
                        <select name="payment_method"
                            class="h-9 rounded-md border border-gray-300 bg-white ps-3 pe-8 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                            onchange="this.form.submit()">
                            <option value="">{{ __('All Payments') }}</option>
                            <option value="cod" {{ request('payment_method') === 'cod' ? 'selected' : '' }}>{{ __('Cash on Delivery') }}</option>
                            <option value="bank_transfer" {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>{{ __('Bank Transfer') }}</option>
                        </select>

                    </form>
                </div>

                <div class="overflow-x-auto w-full">
                    <table class="w-full whitespace-nowrap text-start text-sm text-gray-500">
                        <thead
                            class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-3">{{ __('Order ID / Time') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Customer') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Restaurant') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Driver') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Total') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Payment Method') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Order Status') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Payment Status') }}</th>
                                <th scope="col" class="px-6 py-3 text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="orders-table-body" class="divide-y divide-gray-200 bg-white">
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">#{{ $order->id }}</div>
                                        <div class="text-xs text-gray-500">{{ $order->created_at->format('g:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-8 w-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-xs uppercase">
                                                {{ substr($order->user->name ?? 'U', 0, 2) }}
                                            </div>
                                            <div class="ms-3">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $order->user->name ?? __('Unknown Customer') }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ Str::limit($order->user->email ?? '', 15) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $order->restaurant->name ?? __('Unknown Restaurant') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($order->driver)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $order->driver->name }}
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ __('Unassigned') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        ${{ number_format($order->total, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">
                                        @if($order->payment_method === 'cod')
                                            <span class="text-indigo-600">{{ __('Cash on Delivery') }}</span>
                                        @elseif($order->payment_method === 'bank_transfer' || $order->payment_method === 'bank')
                                            <span class="text-green-600">{{ __('Bank Transfer') }}</span>
                                        @else
                                            <span
                                                class="text-gray-500 capitalize">{{ $order->payment_method ?: __('Not specified') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'pending_admin_approval' => 'bg-orange-100 text-orange-800',
                                                'pending_driver_acceptance' => 'bg-yellow-100 text-yellow-800',
                                                'driver_assigned' => 'bg-blue-100 text-blue-800',
                                                'ready_for_pickup' => 'bg-purple-100 text-purple-800',
                                                'on_the_way' => 'bg-indigo-100 text-indigo-800',
                                                'delivered' => 'bg-green-100 text-green-800',
                                                'canceled' => 'bg-red-100 text-red-800',
                                            ];
                                            $statusLabels = [
                                                'pending_admin_approval' => __('Pending Admin'),
                                                'pending_driver_acceptance' => __('Searching for Driver'),
                                                'driver_assigned' => __('Driver Assigned'),
                                                'ready_for_pickup' => __('Ready for Pickup'),
                                                'on_the_way' => __('On the Way'),
                                                'delivered' => __('Delivered'),
                                                'canceled' => __('Canceled'),
                                            ];
                                            $colorClass = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                                            $labelText = $statusLabels[$order->status] ?? str_replace('_', ' ', $order->status);
                                        @endphp
                                        <div class="flex flex-col">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $colorClass }} uppercase tracking-wide">
                                                {{ $labelText }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $paymentStatusColors = [
                                                'pending_verification' => 'text-orange-600',
                                                'pending_collection' => 'text-yellow-600',
                                                'completed' => 'text-green-600',
                                                'rejected' => 'text-red-600',
                                                'canceled' => 'text-gray-600',
                                                'pending_refund' => 'text-purple-600',
                                                'refunded' => 'text-blue-600',
                                            ];
                                            $paymentColor = $paymentStatusColors[$order->payment_status] ?? 'text-gray-500';
                                        @endphp
                                        <div class="flex items-center gap-1.5">
                                            <div
                                                class="w-1.5 h-1.5 rounded-full {{ str_replace('text-', 'bg-', $paymentColor) }}">
                                            </div>
                                            <span class="text-xs font-semibold {{ $paymentColor }} capitalize">
                                                {{ str_replace('_', ' ', $order->payment_status ?? 'N/A') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="actions-cell px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                        <div class="flex justify-end items-center gap-1.5">
                                            <!-- View Details -->
                                            <button data-order='@json($order->load('logs'))' onclick="openAuditModal(this)"
                                                class="inline-flex items-center gap-1 px-2 py-1.5 rounded-md text-xs font-semibold text-blue-600 bg-blue-50 border border-blue-200 hover:bg-blue-100 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>

                                            </button>
                                            <!-- Reassign Driver -->
                                            <button onclick="openReassignModal({{ $order->id }})"
                                                class="inline-flex items-center gap-1 px-2 py-1.5 rounded-md text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-200 hover:bg-indigo-100 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>

                                            </button>
                                            <!-- Force Cancel -->
                                            <form action="{{ route('admin.orders.force-cancel', $order->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Force cancel order #{{ $order->id }}?');"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1 px-2 py-1.5 rounded-md text-xs font-semibold text-red-600 bg-red-50 border border-red-200 hover:bg-red-100 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>

                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="p-12 flex flex-col items-center justify-center text-center">
                                            <svg class="h-12 w-12 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900">{{ __('No active orders found') }}</h3>
                                            <p class="mt-1 text-sm text-gray-500">{{ __('Wait for new orders to arrive.') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                    {{ $orders->links() }}
                </div>

            </div>
        </main>
    </div>

    <!-- View Details & Audit Trail Modal -->
    <div id="auditModal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm" onclick="closeAuditModal()"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-4xl">
                    <!-- Header -->
                    <div
                        class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between rounded-t-xl">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Order') }} #<span id="auditModalOrderId"></span>
                            &mdash; {{ __('Details & Audit Trail') }}</h3>
                        <button onclick="closeAuditModal()"
                            class="text-gray-400 hover:text-gray-600 rounded-lg p-1.5 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <!-- Body: 2-Column Grid -->
                    <div class="px-6 py-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                                        {{ __('Customer Info') }}</h4>
                                    <div
                                        class="flex items-center space-x-3 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-sm"
                                            id="detailCustomerInitials">?</div>
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
                                    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                                        {{ __('Restaurant & Driver') }}</h4>
                                    <div class="space-y-2 text-sm">
                                        <p><span class="font-medium text-gray-800">Restaurant:</span> <span
                                                id="detailRestaurant">-</span></p>
                                        <p><span class="font-medium text-gray-800">Driver Assigned:</span> <span
                                                id="detailDriver">-</span></p>
                                    </div>
                                </div>
                            </div>
                            <!-- Right Column -->
                            <div>
                                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ __('Order Status') }}</h4>
                                <div
                                    class="bg-indigo-50 border border-indigo-100 rounded-lg p-3 mb-4 flex items-center justify-between">
                                    <span class="text-sm font-medium text-indigo-900 capitalize"
                                        id="detailCurrentBadge">-</span>
                                    <span class="text-xs text-gray-500" id="detailPaymentMethod"></span>
                                </div>
                                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 mt-6">{{ __('Order Items') }}</h4>
                                <div class="bg-gray-50 rounded-lg border border-gray-100 overflow-hidden">
                                    <ul class="divide-y divide-gray-200" id="detailItemsList"></ul>
                                    <div
                                        class="bg-gray-100 px-4 py-3 border-t border-gray-200 flex justify-between items-center">
                                        <span class="text-sm font-semibold text-gray-900">{{ __('Order Total') }}</span>
                                        <span class="text-lg font-bold text-gray-900" id="detailTotal">$0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Audit Trail Timeline -->
                    <div class="px-6 pb-6 pt-4 border-t border-gray-200">
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">{{ __('Audit Trail & Lifecycle Logs') }}</h4>
                        <div id="auditLogsContainer" class="space-y-3">
                            <!-- Populated by JS -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reassign Driver Modal -->
    <div id="reassignModal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm" onclick="closeReassignModal()"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md">
                    <div
                        class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between rounded-t-xl">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Reassign Driver') }}</h3>
                        <button onclick="closeReassignModal()"
                            class="text-gray-400 hover:text-gray-600 rounded-lg p-1.5 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="px-6 py-5">
                        <p class="text-sm text-gray-500 mb-4">{{ __('Select a driver to assign to Order') }} #<span
                                id="reassignModalOrderIdText" class="font-semibold text-gray-900"></span>.</p>
                        <form id="executeReassignForm" method="POST">
                            @csrf
                            <input type="hidden" name="driver_id" id="hiddenDriverIdInput">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Select Driver') }}</label>
                                    <select id="reassignModalDriverSelect"
                                        class="block w-full rounded-md border-gray-300 py-2 px-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required>
                                        <option value="">{{ __('-- Choose a driver --') }}</option>
                                        @foreach($drivers as $driver)
                                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="button" onclick="submitCustomReassign(this)"
                                    class="w-full flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">{{ __('Confirm Assignment') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/app.js'])

    <!-- Scripts -->
    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    <script>
        function openAuditModal(btn) {
            const data = JSON.parse(btn.getAttribute('data-order'));

            // Header
            document.getElementById('auditModalOrderId').innerText = data.id;

            // Customer info
            const name = data.user ? data.user.name : 'Unknown Customer';
            document.getElementById('detailCustomerName').innerText = name;
            document.getElementById('detailCustomerInitials').innerText = name.substring(0, 2).toUpperCase();
            document.getElementById('detailCustomerPhone').innerText = (data.user && data.user.phone) ? data.user.phone : 'No phone on record';
            document.getElementById('detailAddress').innerText = data.address || 'Not provided';

            // Restaurant & Driver
            document.getElementById('detailRestaurant').innerText = data.restaurant ? data.restaurant.name : 'N/A';
            document.getElementById('detailDriver').innerText = data.driver ? data.driver.name : 'Unassigned';

            // Status & Payment
            document.getElementById('detailCurrentBadge').innerText = data.status ? data.status.replace(/_/g, ' ') : '-';
            const payMap = { cod: 'Cash on Delivery', bank: 'Bank Transfer' };
            document.getElementById('detailPaymentMethod').innerText = payMap[data.payment_method] || (data.payment_method || '');

            // Total
            document.getElementById('detailTotal').innerText = '$' + Number(data.total || 0).toFixed(2);

            // Items placeholder
            document.getElementById('detailItemsList').innerHTML = '<li class="px-4 py-3 text-sm text-gray-500 italic">{{ __('Item details not included in quick view.') }}</li>';

            // Audit Logs
            let logsHtml = '';
            if (data.logs && data.logs.length > 0) {
                data.logs.forEach(log => {
                    const date = new Date(log.created_at).toLocaleString('en-US', { dateStyle: 'medium', timeStyle: 'short' });
                    logsHtml += `
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 h-8 w-8 bg-indigo-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="flex-1 bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <p class="text-sm font-medium text-gray-900">${log.description}</p>
                            <div class="mt-1 flex items-center gap-2 text-xs">
                                <span class="capitalize font-semibold text-indigo-600">${log.status.replace(/_/g, ' ')}</span>
                                <span class="text-gray-400">&middot;</span>
                                <span class="text-gray-500">${date}</span>
                            </div>
                        </div>
                    </div>`;
                });
            } else {
                logsHtml = '<p class="text-sm text-gray-500 italic">{{ __('No audit log entries found for this order.') }}</p>';
            }
            document.getElementById('auditLogsContainer').innerHTML = logsHtml;

            // Show modal
            document.getElementById('auditModal').classList.remove('hidden');
        }

        function closeAuditModal() {
            document.getElementById('auditModal').classList.add('hidden');
        }

        function openReassignModal(orderId) {
            document.getElementById('reassignModalOrderIdText').innerText = orderId;
            document.getElementById('executeReassignForm').action = '/admin/orders/' + orderId + '/reassign';
            document.getElementById('reassignModalDriverSelect').value = '';
            document.getElementById('reassignModal').classList.remove('hidden');
        }

        function closeReassignModal() {
            document.getElementById('reassignModal').classList.add('hidden');
        }

        async function submitCustomReassign(submitBtn) {
            const drv = document.getElementById('reassignModalDriverSelect').value;
            const form = document.getElementById('executeReassignForm');

            if (!drv) {
                alert('Please select a driver first.');
                return;
            }

            // Disable button and show loading state
            const originalText = submitBtn.innerText;
            submitBtn.disabled = true;
            submitBtn.innerText = '{{ __("Processing...") }}';

            const formData = new FormData(form);
            formData.set('driver_id', drv);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    // Success: Reload the page to show the updated assignment
                    window.location.reload();
                } else {
                    const result = await response.json();
                    // Display the Arabic error message from the backend
                    alert(result.message || '{{ __("An unexpected error occurred") }}');
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalText;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('{{ __("Connection error. Please try again.") }}');
                submitBtn.disabled = false;
                submitBtn.innerText = originalText;
            }
        }

        // ── Live Search: debounce customer name input 500ms ──
        (function () {
            var input = document.getElementById('customerNameInput');
            var form = document.getElementById('ordersFilterForm');
            if (!input || !form) return;
            var timer;
            input.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(function () { form.submit(); }, 500);
            });
        })();
    </script>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- Real-Time Orders via Laravel Reverb (WebSockets)       --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <script>
    (function () {
        // ── Guard: ensure Echo is available ──────────────────────────
        if (typeof window.Echo === 'undefined') {
            console.warn('[RT-Orders] Laravel Echo not initialized. Is Vite running?');
            return;
        }

        // ── Status helpers (mirror the Blade @php maps above) ────────
        const STATUS_COLORS = {
            pending_admin_approval:    'bg-orange-100 text-orange-800',
            pending_driver_acceptance: 'bg-yellow-100 text-yellow-800',
            driver_assigned:           'bg-blue-100 text-blue-800',
            ready_for_pickup:          'bg-purple-100 text-purple-800',
            on_the_way:               'bg-indigo-100 text-indigo-800',
            delivered:                 'bg-green-100 text-green-800',
            canceled:                  'bg-red-100 text-red-800',
        };
        const STATUS_LABELS = {
            pending_admin_approval:    '{{ __('Pending Admin') }}',
            pending_driver_acceptance: '{{ __('Searching for Driver') }}',
            driver_assigned:           '{{ __('Driver Assigned') }}',
            ready_for_pickup:          '{{ __('Ready for Pickup') }}',
            on_the_way:               '{{ __('On the Way') }}',
            delivered:                 '{{ __('Delivered') }}',
            canceled:                  '{{ __('Canceled') }}',
        };
        const PAYMENT_STATUS_COLORS = {
            pending_verification: 'text-orange-600',
            pending_collection:   'text-yellow-600',
            completed:            'text-green-600',
            rejected:             'text-red-600',
            canceled:             'text-gray-600',
            pending_refund:       'text-purple-600',
            refunded:             'text-blue-600',
        };

        function formatPaymentMethod(method) {
            if (method === 'cod') return '<span class="text-indigo-600">{{ __('Cash on Delivery') }}</span>';
            if (method === 'bank_transfer' || method === 'bank') return '<span class="text-green-600">{{ __('Bank Transfer') }}</span>';
            return '<span class="text-gray-500 capitalize">' + (method || '{{ __('Not specified') }}') + '</span>';
        }

        function buildRow(order) {
            const now        = new Date();
            const timeStr    = now.toLocaleString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
            const customer   = order.user || {};
            const initials   = (customer.name || 'U').substring(0, 2).toUpperCase();
            const statusColor = STATUS_COLORS[order.status] || 'bg-gray-100 text-gray-800';
            const statusLabel = STATUS_LABELS[order.status] || (order.status || '').replace(/_/g, ' ');
            const payColor   = PAYMENT_STATUS_COLORS[order.payment_status] || 'text-gray-500';
            const dotColor   = payColor.replace('text-', 'bg-');
            const payLabel   = (order.payment_status || 'N/A').replace(/_/g, ' ');
            const total      = parseFloat(order.total || 0).toFixed(2);
            const orderId    = order.id;

            return `
            <tr id="rt-order-${orderId}" class="hover:bg-gray-50 transition-colors rt-new-order" style="background-color:#f0fdf4;">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">#${orderId}</div>
                    <div class="text-xs text-gray-500">${timeStr}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-8 w-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-xs uppercase">
                            ${initials}
                        </div>
                        <div class="ms-3">
                            <div class="text-sm font-medium text-gray-900">${customer.name || '{{ __('Unknown Customer') }}'}</div>
                            <div class="text-xs text-gray-500">${(customer.email || '').substring(0, 15)}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">${order.restaurant_name || '{{ __('Unknown Restaurant') }}'}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ __('Unassigned') }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">$${total}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">
                    ${formatPaymentMethod(order.payment_method)}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex flex-col">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold ${statusColor} uppercase tracking-wide">
                            ${statusLabel}
                        </span>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center gap-1.5">
                        <div class="w-1.5 h-1.5 rounded-full ${dotColor}"></div>
                        <span class="text-xs font-semibold ${payColor} capitalize">${payLabel}</span>
                    </div>
                </td>
                <td class="actions-cell px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                    <div class="flex justify-end items-center gap-1.5">
                        <span class="inline-flex items-center gap-1 px-2 py-1.5 rounded-md text-xs font-semibold text-gray-400 bg-gray-50 border border-gray-200">{{ __('New') }}</span>
                    </div>
                </td>
            </tr>`;
        }

        function playNotificationSound() {
            try {
                // Simple beep via AudioContext (no file needed)
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, ctx.currentTime);
                gain.gain.setValueAtTime(0.15, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.6);
            } catch (e) { /* AudioContext blocked — silent fail */ }
        }

        function fadeOutHighlight(rowId) {
            setTimeout(function () {
                const row = document.getElementById('rt-order-' + rowId);
                if (row) {
                    row.style.transition = 'background-color 1.5s ease';
                    row.style.backgroundColor = '';
                }
            }, 4000); // hold green for 4s then fade
        }

        // ── Subscribe to the private admin channel ────────────────────
        window.Echo.private('admin.orders')
            .listen('.OrderBroadcasted', function (e) {
                const order = e.order;
                if (!order) return;

                console.log('[RT-Orders] New order received:', order);

                const tbody = document.getElementById('orders-table-body');
                if (!tbody) return;

                // Remove empty-state row if present
                const emptyRow = tbody.querySelector('td[colspan]');
                if (emptyRow) emptyRow.closest('tr').remove();

                // Inject new row at top
                tbody.insertAdjacentHTML('afterbegin', buildRow(order));

                // Subtle green highlight → fades out after 4s
                fadeOutHighlight(order.id);

                // Notification sound
                playNotificationSound();
            });

        console.log('[RT-Orders] Listening on private channel: admin.orders');
    })();
    </script>
</body>

</html>