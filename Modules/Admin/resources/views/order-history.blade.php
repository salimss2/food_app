<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Order History') }} - {{ __('Admin Dashboard') }}</title>
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

        /* Select arrow flip */
        html[dir="rtl"] select {
            background-position: left 0.5rem center !important;
            padding-left: 2rem !important;
            padding-right: 0.75rem !important;
        }

        /* Table alignment */
        html[dir="rtl"] table {
            text-align: right;
        }

        html[dir="rtl"] .actions-cell {
            text-align: left !important;
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

            <div
                class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ __('Order History') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ __('Archive of all completed and cancelled past orders.') }}
                    </p>
                </div>
                <div class="flex space-x-2">
                    <select id="historyFilter"
                        class="block w-full rounded-lg border-gray-300 py-2 ps-3 pe-10 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary shadow-sm bg-white"
                        onchange="renderHistoryTable()">
                        <option value="All">{{ __('All History') }}</option>
                        <option value="Completed">{{ __('Completed Only') }}</option>
                        <option value="Cancelled">{{ __('Cancelled Only') }}</option>
                    </select>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto w-full">
                    <table class="w-full whitespace-nowrap text-start text-sm text-gray-500">
                        <thead
                            class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-3">{{ __('Order ID / Date') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Customer') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Restaurant') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Total') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Status') }}</th>
                                <th scope="col" class="px-6 py-3 text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">#{{ $order->id }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $order->created_at->format('M d, Y - g:i A') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-8 w-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center font-bold text-xs uppercase">
                                                {{ substr($order->user->name ?? 'U', 0, 2) }}
                                            </div>
                                            <div class="ms-3">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $order->user->name ?? __('Unknown Customer') }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $order->restaurant->name ?? __('Unknown Restaurant') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                        ${{ number_format($order->total, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($order->status === 'delivered')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ __('Delivered') }}
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                {{ __('Cancelled') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="actions-cell px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                        <button onclick="viewHistoryDetails({{ $order->id }})" title="Trail"
                                            class="text-gray-400 hover:text-primary focus:outline-none bg-gray-50 hover:bg-gray-100 p-1.5 rounded transition-colors inline-block">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div
                                            class="p-12 flex flex-col items-center justify-center text-center border-t border-gray-200">
                                            <h3 class="text-lg font-medium text-gray-900 mt-2">
                                                {{ __('No history records found') }}
                                            </h3>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Placeholder -->
                <div class="border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                    {{ $orders->links() }}
                </div>
            </div>
        </main>
    </div>

    <!-- Order Details Modal (Read-Only) -->
    <div id="orderDetailsModal" class="relative z-40 hidden-el" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-xl bg-white text-start shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl modal-content">
                    <!-- Header -->
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2" id="modalOrderId">
                            {{ __('Order Reference') }}
                        </h3>
                        <button onclick="closeModal('orderDetailsModal')"
                            class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="px-6 py-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Left Col: Customer & Restaurant -->
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                                        {{ __('Customer Info') }}
                                    </h4>
                                    <div
                                        class="flex items-center space-x-3 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        <div class="flex-shrink-0 h-10 w-10 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center font-bold"
                                            id="detailCustomerInitials">
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900" id="detailCustomerName">-</p>
                                            <p class="text-xs text-gray-500" id="detailCustomerPhone">-</p>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-600">
                                        <span class="font-medium text-gray-800">{{ __('Delivered To:') }}</span>
                                        <p class="mt-1" id="detailAddress">-</p>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                                        {{ __('Restaurant & Driver') }}
                                    </h4>
                                    <div class="space-y-2 text-sm">
                                        <p><span class="font-medium text-gray-800">Restaurant:</span> <span
                                                id="detailRestaurant">-</span></p>
                                        <p><span class="font-medium text-gray-800">Driver Assigned:</span> <span
                                                id="detailDriver">-</span></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Col: Timeline & Items -->
                            <div>
                                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                                    {{ __('Final Status') }}
                                </h4>
                                <div class="mb-4 flex items-center">
                                    <span id="detailCurrentBadge"></span>
                                    <span class="ms-3 text-xs text-gray-500" id="detailTime"></span>
                                </div>

                                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 mt-6">
                                    {{ __('Order Items Archive') }}
                                </h4>
                                <div class="bg-gray-50 rounded-lg border border-gray-100 overflow-hidden opacity-90">
                                    <ul class="divide-y divide-gray-200" id="detailItemsList">
                                    </ul>
                                    <div
                                        class="bg-gray-100 px-4 py-3 border-t border-gray-200 flex justify-between items-center sm:px-6">
                                        <span class="text-sm font-semibold text-gray-900">{{ __('Final Price') }}</span>
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
    <script>
        function viewHistoryDetails(orderId) {
            alert('{{ __('Audit Trail details for Order #') }}' + orderId + ' {{ __('will be shown here.') }}');
        }
    </script>
</body>

</html>