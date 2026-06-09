<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revenue Management - Admin Dashboard</title>
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
        html[dir="rtl"] body { font-family: 'Cairo', sans-serif !important; }
        html[dir="rtl"] .ml-3 { margin-left: 0 !important; margin-right: 0.75rem !important; }
        html[dir="rtl"] .ml-4 { margin-left: 0 !important; margin-right: 1rem !important; }
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
            
            <!-- Toast Alert -->
            <div id="toast" class="hidden-el fixed bottom-4 right-4 z-50 rounded-md bg-green-50 p-4 shadow-lg border border-green-200 transition-opacity duration-300">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400 border-green-200" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800" id="toastMessage">Success</p>
                    </div>
                </div>
            </div>

            <div class="mb-6 flex flex-col justify-between items-start space-y-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Financial Revenue Check</h2>
                    <p class="text-sm text-gray-500 mt-1">Review live injections, pending vendor withdrawals, and historic processed checks.</p>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8 mt-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 pl-6 border-l-4 border-l-green-500">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Total Lifetime Earnings</h3>
                    <p class="text-3xl font-extrabold text-green-600 mt-2">$240,510.82</p>
                    <p class="text-xs text-gray-400 mt-2">Sum of all successfully settled customer payments.</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 pl-6 border-l-4 border-l-yellow-500">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Pending Vendor Withdrawals</h3>
                    <p class="text-3xl font-extrabold text-gray-900 mt-2">$15,400.00</p>
                    <p class="text-xs text-gray-400 mt-2">Unsettled cash tied up in <a href="{{ route('admin.withdrawals.index') }}" class="text-primary hover:underline">12 requests.</a></p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 pl-6 border-l-4 border-l-blue-500">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Processed Payments Out</h3>
                    <p class="text-3xl font-extrabold text-gray-900 mt-2">$85,200.00</p>
                    <p class="text-xs text-gray-400 mt-2">Liquid capital transferred directly to vendor banks.</p>
                </div>
            </div>

            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Recent Transaction Flow</h3>
                <div class="flex space-x-2">
                    <select id="typeFilter" class="block rounded-lg border-gray-300 py-1 pl-3 pr-8 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary shadow-sm bg-white" onchange="renderTable()">
                        <option value="All">All Transfers</option>
                        <option value="Inbound">Inbound (Income)</option>
                        <option value="Outbound">Outbound (Payout)</option>
                    </select>
                </div>
            </div>

            <!-- List Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto w-full">
                    <table class="w-full whitespace-nowrap text-left text-sm text-gray-500">
                        <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-3">Transaction ID</th>
                                <th scope="col" class="px-6 py-3">Type</th>
                                <th scope="col" class="px-6 py-3">Entity</th>
                                <th scope="col" class="px-6 py-3 text-right">Amount</th>
                                <th scope="col" class="px-6 py-3">Date Processed</th>
                                <th scope="col" class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="revTableBody" class="divide-y divide-gray-200 bg-white">
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="hidden-el p-12 flex flex-col items-center justify-center text-center border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mt-2">No transactions recorded</h3>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    <script src="{{ asset('modules/admin/js/revenue.js') }}"></script>
</body>
</html>
