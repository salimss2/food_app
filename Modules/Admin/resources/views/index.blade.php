@extends('admin::layouts.app')

@section('content')
    <!-- Page Title -->
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Dashboard Overview') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Welcome back, Admin. Here\'s what\'s happening today.') }}</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <button
                class="bg-primary hover:bg-primary_dark text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <span>{{ __('Generate Report') }}</span>
            </button>
        </div>
    </div>

    <!-- Stats grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
        <!-- Stat Card 1 -->
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3 hover:shadow-md transition-shadow overflow-hidden">
            <div class="flex-shrink-0 p-2.5 rounded-full bg-blue-50 text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-gray-500 truncate">{{ __('Total Users') }}</p>
                <p class="text-lg sm:text-xl font-bold text-gray-900 whitespace-nowrap counter-up"
                    data-target="{{ $totalUsersCount }}">{{ $totalUsersCount }}</p>
            </div>
        </div>

        <!-- Stat Card 2 -->
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3 hover:shadow-md transition-shadow overflow-hidden">
            <div class="flex-shrink-0 p-2.5 rounded-full bg-indigo-50 text-indigo-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-gray-500 truncate">{{ __('Active Orders') }}</p>
                <p class="text-lg sm:text-xl font-bold text-gray-900 whitespace-nowrap counter-up"
                    data-target="{{ $activeOrdersCount }}">0</p>
            </div>
        </div>

        <!-- Stat Card 3 -->
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3 hover:shadow-md transition-shadow overflow-hidden">
            <div class="flex-shrink-0 p-2.5 rounded-full bg-green-50 text-green-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-gray-500 truncate">{{ __('Restaurants') }}</p>
                <p class="text-lg sm:text-xl font-bold text-gray-900 whitespace-nowrap counter-up"
                    data-target="{{ $restaurantsCount }}">{{ $restaurantsCount }}</p>
            </div>
        </div>

        <!-- Stat Card 4 -->
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3 hover:shadow-md transition-shadow overflow-hidden">
            <div class="flex-shrink-0 p-2.5 rounded-full bg-yellow-50 text-yellow-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-gray-500 truncate">{{ __('Pending Payments') }}</p>
                <p id="fb-pending-payments" class="text-base sm:text-lg font-bold text-gray-900 whitespace-nowrap truncate">
                    $<span class="counter-up-currency"
                        data-target="{{ $pendingPaymentsTotal }}">{{ number_format($pendingPaymentsTotal, 2) }}</span></p>
            </div>
        </div>

        <!-- Stat Card 5 (Today's Revenue) -->
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3 hover:shadow-md transition-shadow overflow-hidden">
            <div class="flex-shrink-0 p-2.5 rounded-full bg-emerald-50 text-emerald-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-gray-500 truncate">{{ __('Today\'s Revenue') }}</p>
                <p class="text-base sm:text-lg font-bold text-gray-900 whitespace-nowrap truncate"><span class="counter-up"
                        data-target="{{ $todayRevenue }}">{{ number_format($todayRevenue) }}</span> YER</p>
            </div>
        </div>

        <!-- Stat Card 6 (Online Drivers) -->
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3 hover:shadow-md transition-shadow overflow-hidden">
            <div class="flex-shrink-0 p-2.5 rounded-full bg-purple-50 text-purple-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-gray-500 truncate">{{ __('Online Drivers') }}</p>
                <p class="text-lg sm:text-xl font-bold text-gray-900 whitespace-nowrap counter-up"
                    data-target="{{ $onlineDriversCount }}">{{ $onlineDriversCount }}</p>
            </div>
        </div>
    </div>

    <!-- Recent Activity Skeleton (Placeholder for Dashboard) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-5">
        <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Quick Links') }}</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.users.index') }}"
                class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-indigo-300 transition-colors">
                <div class="text-sm font-medium text-gray-900">{{ __('Manage Users') }} &rarr;</div>
                <div class="mt-1 text-xs text-gray-500">{{ __('View, edit, or block users.') }}</div>
            </a>
            <a href="{{ route('admin.drivers.index') }}"
                class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-indigo-300 transition-colors">
                <div class="text-sm font-medium text-gray-900">{{ __('Manage Drivers') }} &rarr;</div>
                <div class="mt-1 text-xs text-gray-500">{{ __('Onboard new delivery drivers.') }}</div>
            </a>
            <a href="{{ route('admin.restaurants.index') }}"
                class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-indigo-300 transition-colors">
                <div class="text-sm font-medium text-gray-900">{{ __('Manage Restaurants') }} &rarr;</div>
                <div class="mt-1 text-xs text-gray-500">{{ __('Add food menus and restaurants.') }}</div>
            </a>
            <a href="{{ route('admin.payments.index') }}"
                class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-indigo-300 transition-colors">
                <div class="text-sm font-medium text-gray-900">{{ __('Approve Payments') }} &rarr;</div>
                <div class="mt-1 text-xs text-gray-500">{{ __('Process wire deposits manually.') }}</div>
            </a>
        </div>
    </div>

    <!-- Firebase Real-time Latest Orders Table -->
    <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-5">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900 flex items-center space-x-2">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                </span>
                <span>{{ __('Latest Orders (Live)') }}</span>
            </h3>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-primary hover:text-primary_dark">{{ __('View All') }}
                &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Order ID') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Restaurant') }}</th>
                        <th scope="col"
                            class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Customer') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Time') }}</th>
                    </tr>
                </thead>
                <tbody id="fb-orders-table-body" class="bg-white divide-y divide-gray-200">
                    @foreach($latestOrders as $order)
                        <tr id="fb-order-{{ $order->id }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #{{ $order->order_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->restaurant->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($order->total, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'pending_driver_acceptance' => 'bg-purple-100 text-purple-800',
                                        'driver_assigned' => 'bg-indigo-100 text-indigo-800',
                                        'preparing' => 'bg-orange-100 text-orange-800',
                                        'picked_up' => 'bg-blue-100 text-blue-800',
                                        'delivered' => 'bg-green-100 text-green-800',
                                        'canceled' => 'bg-red-100 text-red-800',
                                    ];
                                    $class = $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800';
                                    $label = ucwords(str_replace('_', ' ', $order->status));
                                @endphp
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $class }}">{{ $label }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                    @if($latestOrders->isEmpty())
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">{{ __('No orders found.') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Firebase Realtime integration -->
    @include('admin::partials.firebase-scripts')

    <!-- CounterUp Logic -->
    <style>
        .counter-up,
        .counter-up-currency {
            font-variant-numeric: tabular-nums;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const speed = 60;

            // Logic for standard integer counters
            document.querySelectorAll('.counter-up').forEach(counter => {
                const target = +counter.getAttribute('data-target');
                if (isNaN(target) || target === 0) return;

                // Reset to 0 initially to start animation
                counter.innerText = '0';

                const updateCount = () => {
                    const currentText = counter.innerText.replace(/[^\d.-]/g, '');
                    const count = parseFloat(currentText) || 0;
                    const inc = target / speed;

                    if (count < target) {
                        const nextCount = Math.ceil(count + inc);
                        // Clamp at target to prevent overshooting
                        const finalCount = nextCount > target ? target : nextCount;
                        counter.innerText = finalCount.toLocaleString('en-US');

                        if (finalCount < target) {
                            setTimeout(updateCount, 25);
                        }
                    }
                };
                updateCount();
            });

            // Logic for currency counters
            document.querySelectorAll('.counter-up-currency').forEach(counter => {
                const target = +counter.getAttribute('data-target');
                if (isNaN(target) || target === 0) return;

                counter.innerText = '0.00';

                const updateCount = () => {
                    const currentText = counter.innerText.replace(/[^\d.-]/g, '');
                    const count = parseFloat(currentText) || 0;
                    const inc = target / speed;

                    if (count < target) {
                        const nextCount = count + inc;
                        const finalCount = nextCount > target ? target : nextCount;
                        counter.innerText = finalCount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                        if (finalCount < target) {
                            setTimeout(updateCount, 25);
                        }
                    }
                };
                updateCount();
            });
        });
    </script>
@endpush