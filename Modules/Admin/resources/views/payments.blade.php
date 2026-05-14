<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Pending Payments') }} - {{ __('Admin Dashboard') }}</title>
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
        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto w-full">

            <div
                class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ __('Pending Payments') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ __('Review and approve customer bank transfers.') }}</p>
                </div>
            </div>

            <!-- Quick Metrics -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Total Pending -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center space-x-reverse space-x-4">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('Pending Verification') }}</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $totalPending ?? 0 }}</h3>
                    </div>
                </div>

                <!-- Pending Collection -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center space-x-reverse space-x-4">
                    <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('Pending Collection') }}</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $totalPendingCollection ?? 0 }}</h3>
                    </div>
                </div>

                <!-- Total Canceled -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center space-x-reverse space-x-4">
                    <div class="p-3 rounded-full bg-gray-100 text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('Canceled') }}</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $totalCanceled ?? 0 }}</h3>
                    </div>
                </div>

                <!-- Total Processed -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center space-x-reverse space-x-4">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('Total Processed') }}</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $totalProcessed ?? 0 }}</h3>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="mb-4 border-b border-gray-200 flex space-x-reverse space-x-4">
                <button onclick="switchTab('all')" id="tab-all"
                    class="py-2 px-4 font-semibold text-sm border-b-2 text-primary border-primary transition-colors">{{ __('All Payments') }}</button>
                <button onclick="switchTab('pending_refund')" id="tab-pending_refund"
                    class="py-2 px-4 font-semibold text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-colors flex items-center space-x-reverse space-x-2">
                    <span>{{ __('Pending Refunds') }}</span>
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">{{ __('New') }}</span>
                </button>
            </div>

            <!-- Filters & Table Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Filters -->
                <div class="p-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex flex-col sm:flex-row flex-wrap gap-3 items-end">

                        <!-- From Date -->
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">{{ __('From Date') }}</label>
                            <input type="date" id="filterFromDate"
                                class="block rounded border-gray-300 py-1.5 px-2 text-sm shadow-sm focus:ring-primary focus:border-primary bg-white">
                        </div>

                        <!-- To Date -->
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">{{ __('To Date') }}</label>
                            <input type="date" id="filterToDate"
                                class="block rounded border-gray-300 py-1.5 px-2 text-sm shadow-sm focus:ring-primary focus:border-primary bg-white">
                        </div>

                        <!-- Min Amount -->
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">{{ __('Min Amount ($)') }}</label>
                            <input type="number" id="filterMinAmount" placeholder="e.g. 50"
                                class="block w-28 rounded border-gray-300 py-1.5 px-2 text-sm shadow-sm focus:ring-primary focus:border-primary bg-white">
                        </div>

                        <!-- Order Status -->
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">{{ __('Order Status') }}</label>
                            <select id="filterOrderStatus"
                                class="block w-48 rounded border-gray-300 py-1.5 px-2 text-sm shadow-sm focus:ring-primary focus:border-primary bg-white">
                                <option value="all">{{ __('All Order Status') }}</option>
                                <option value="pending_admin_approval">{{ __('Pending Admin Approval') }}</option>
                                <option value="pending_driver_acceptance">{{ __('Pending Driver Acceptance') }}</option>
                                <option value="driver_assigned">{{ __('Driver Assigned') }}</option>
                                <option value="ready_for_pickup">{{ __('Ready for Pickup') }}</option>
                                <option value="on_the_way">{{ __('On the Way') }}</option>
                                <option value="delivered">{{ __('Delivered') }}</option>
                                <option value="canceled">{{ __('Canceled') }}</option>
                            </select>
                        </div>

                        <!-- Payment Status -->
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">{{ __('Payment Status') }}</label>
                            <select id="ajaxStatusFilter"
                                class="block w-48 rounded border-gray-300 py-1.5 px-2 text-sm shadow-sm focus:ring-primary focus:border-primary bg-white">
                                <option value="all">{{ __('All Payment Status') }}</option>
                                <option value="pending_verification">{{ __('Pending Verification') }}</option>
                                <option value="pending_collection">{{ __('Pending Collection') }}</option>
                                <option value="completed">{{ __('Completed') }}</option>
                                <option value="rejected">{{ __('Rejected') }}</option>
                                <option value="canceled">{{ __('Canceled') }}</option>
                                <option value="pending_refund">{{ __('Pending Refund') }}</option>
                                <option value="refunded">{{ __('Refunded') }}</option>
                            </select>
                        </div>

                        <!-- Loading Indicator -->
                        <div id="loadingIndicator"
                            class="hidden items-center space-x-reverse space-x-2 text-sm text-gray-500 self-end pb-1">
                            <svg class="animate-spin h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>{{ __('Loading...') }}</span>
                        </div>

                    </div>
                </div>

                <!-- Table Wrapper -->
                <div class="overflow-x-auto w-full">
                    <table class="w-full whitespace-nowrap text-start text-sm text-gray-500">
                        <thead
                            class="bg-gray-100 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-3">{{ __('Order ID / Date') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Customer') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Payment Method') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Total Amount') }}</th>
                                <th scope="col" class="px-6 py-3 text-center">{{ __('Payment Proof') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Order Status') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Payment Status') }}</th>
                                <th scope="col" class="px-6 py-3 text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="paymentsTableBody" class="divide-y divide-gray-200 bg-white">
                            @include('admin::partials.payments-table-body', ['orders' => $orders])
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($orders->hasPages())
                    <div class="p-4 border-t border-gray-200 bg-white">
                        {{ $orders->links() }}
                    </div>
                @endif

                <!-- Empty State (Hidden initially) -->
                @if ($orders->isEmpty())
                    <div id="emptyState" class="p-12 flex flex-col items-center justify-center text-center">
                        <div class="bg-gray-50 p-4 rounded-full mb-4">
                            <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('No pending payments') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('All payments have been processed or no matching records found.') }}</p>
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- Image Preview Modal -->
    <div id="imageModal" class="relative z-50 hidden-el" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-80 transition-opacity modal-overlay"
            onclick="closeModal('imageModal')"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-xl bg-white shadow-xl transition-all modal-content w-full max-w-3xl">
                    <div class="flex justify-between items-center bg-gray-900 p-3">
                        <h3 class="text-white font-medium" id="previewTitle">{{ __('Payment Proof') }}</h3>
                        <button onclick="closeModal('imageModal')"
                            class="text-gray-300 hover:text-white focus:outline-none">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="bg-black p-2 flex justify-center items-center min-h-[50vh]">
                        <img id="fullImage" src="" alt="Proof" class="max-w-full max-h-[80vh] object-contain">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Order Modal -->
    <div id="cancelOrderModal" class="relative z-50 hidden-el" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"
            onclick="closeModal('cancelOrderModal')"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-xl bg-white text-start shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md modal-content">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ms-4 sm:mt-0 sm:text-start w-full">
                                <h3 class="text-base font-semibold leading-6 text-gray-900" id="cancelModalTitle">
                                    {{ __('Cancel Order') }}</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-3">
                                        {{ __('Please provide a reason for canceling this order. This action cannot be undone.') }}
                                    </p>
                                    <textarea id="cancellationReasonInput" rows="3"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 border"
                                        placeholder="{{ __('Cancellation reason...') }}"></textarea>
                                    <p id="cancellationReasonError" class="text-xs text-red-600 mt-1 hidden-el">
                                        {{ __('Reason is required.') }}</p>
                                    <input type="hidden" id="cancelOrderId">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" onclick="submitCancelOrder()"
                            class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ms-3 sm:w-auto transition-colors">{{ __('Confirm Cancellation') }}</button>
                        <button type="button" onclick="closeModal('cancelOrderModal')"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">{{ __('Cancel') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="relative z-50 hidden-el" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-xl bg-white text-start shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md modal-content">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start" id="confirmModalBody">
                            <!-- Populated dynamically -->
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6" id="confirmModalFooter">
                        <!-- Populated dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    <script src="{{ asset('modules/admin/js/payments.js') }}"></script>
</body>

</html>