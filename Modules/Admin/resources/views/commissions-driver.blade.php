<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Commissions - Admin Dashboard</title>
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
                        danger: '#ef4444'
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
        <main class="flex-1 overflow-y-auto w-full bg-gray-50">
            
            <!-- Sub Navigation Tabs -->
            <div class="bg-white border-b border-gray-200 px-4 sm:px-6 lg:px-8 pt-4">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="{{ route('admin.commissions.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors">
                        Overview
                    </a>
                    <a href="{{ route('admin.commissions-restaurant.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors">
                        Restaurant Commissions
                    </a>
                    <a href="{{ route('admin.commissions-driver.index') }}" class="border-primary text-primary whitespace-nowrap border-b-2 py-4 px-1 text-sm font-bold">
                        Driver Commissions
                    </a>
                </nav>
            </div>

            <div class="p-4 sm:p-6 lg:p-8">
                
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Fleet Summaries</h2>
                        <p class="text-sm text-gray-500 mt-1">Review aggregated delivery earnings and system commissions per driver.</p>
                    </div>
                </div>

                <!-- List Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto w-full">
                        <table class="w-full whitespace-nowrap text-left text-sm text-gray-500">
                            <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Driver Name</th>
                                    <th scope="col" class="px-6 py-3 text-center">Deliveries</th>
                                    <th scope="col" class="px-6 py-3 text-right">Total Earnings</th>
                                    <th scope="col" class="px-6 py-3 text-center">Comm. %</th>
                                    <th scope="col" class="px-6 py-3 text-right text-red-600 bg-red-50/30">System Cut</th>
                                    <th scope="col" class="px-6 py-3 text-right text-green-600 bg-green-50/30">Net Payable</th>
                                    <th scope="col" class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="driverTableBody" class="divide-y divide-gray-200 bg-white">
                                <!-- Populated by JS -->
                            </tbody>
                        </table>
                    </div>
                    <!-- Empty State -->
                    <div id="emptyState" class="hidden-el p-12 flex flex-col items-center justify-center text-center border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mt-2">No data found</h3>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Breakdown Modal -->
    <div id="breakdownModal" class="relative z-40 hidden-el" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-3xl modal-content">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900" id="breakdownTitle">Earnings Breakdown</h3>
                        <button onclick="document.getElementById('breakdownModal').classList.add('hidden-el')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    
                    <div class="px-6 py-5">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-sm font-medium text-gray-500">Showing recent 5 deliveries.</span>
                            <button class="text-primary text-sm font-bold hover:underline">Export CSV</button>
                        </div>
                        <div class="border border-gray-200 rounded-md overflow-hidden">
                            <table class="w-full text-left text-sm text-gray-500">
                                <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                                    <tr>
                                        <th class="px-4 py-2">Delivery ID</th>
                                        <th class="px-4 py-2">Date</th>
                                        <th class="px-4 py-2 text-right">Distance</th>
                                        <th class="px-4 py-2 text-right">Base Earn</th>
                                        <th class="px-4 py-2 text-right">Fee (10%)</th>
                                        <th class="px-4 py-2 text-right">Net Earn</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr>
                                        <td class="px-4 py-2 font-medium">#DEL-1011</td>
                                        <td class="px-4 py-2">Today, 2:30 PM</td>
                                        <td class="px-4 py-2 text-right">3.2 km</td>
                                        <td class="px-4 py-2 text-right font-bold text-gray-900">$5.00</td>
                                        <td class="px-4 py-2 text-right text-red-600">-$0.50</td>
                                        <td class="px-4 py-2 text-right text-green-600 font-bold">$4.50</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-2 font-medium">#DEL-1010</td>
                                        <td class="px-4 py-2">Today, 1:15 PM</td>
                                        <td class="px-4 py-2 text-right">1.5 km</td>
                                        <td class="px-4 py-2 text-right font-bold text-gray-900">$3.50</td>
                                        <td class="px-4 py-2 text-right text-red-600">-$0.35</td>
                                        <td class="px-4 py-2 text-right text-green-600 font-bold">$3.15</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-2 font-medium">#DEL-0099</td>
                                        <td class="px-4 py-2">Yesterday, 8:40 PM</td>
                                        <td class="px-4 py-2 text-right">8.0 km</td>
                                        <td class="px-4 py-2 text-right font-bold text-gray-900">$12.00</td>
                                        <td class="px-4 py-2 text-right text-red-600">-$1.20</td>
                                        <td class="px-4 py-2 text-right text-green-600 font-bold">$10.80</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    <script src="{{ asset('modules/admin/js/commissions-driver.js') }}"></script>
</body>
</html>
