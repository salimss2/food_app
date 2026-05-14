<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offers Management - Admin Dashboard</title>
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

            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Promotional Offers</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage public visibility banners, menu discounts, and promotional campaigns.</p>
                </div>
                <div class="flex space-x-2">
                    <select id="offerFilter" class="block w-full rounded-lg border-gray-300 py-2 pl-3 pr-10 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary shadow-sm bg-white" onchange="renderTable()">
                        <option value="All">All Statuses</option>
                        <option value="Active">Active</option>
                        <option value="Expired">Expired</option>
                    </select>
                    <button onclick="openModal()" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary_dark focus:outline-none transition-colors whitespace-nowrap">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Create Offer
                    </button>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center">
                    <div class="p-3 rounded-full bg-indigo-50 text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 uppercase">Live Offers</p>
                        <h3 class="text-2xl font-bold text-gray-900">4</h3>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center">
                    <div class="p-3 rounded-full bg-green-50 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 uppercase">Total Redemptions</p>
                        <h3 class="text-2xl font-bold text-gray-900">2,405</h3>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center">
                    <div class="p-3 rounded-full bg-yellow-50 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 uppercase">Redeemed Today</p>
                        <h3 class="text-2xl font-bold text-gray-900">142</h3>
                    </div>
                </div>
            </div>

            <!-- List Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto w-full">
                    <table class="w-full whitespace-nowrap text-left text-sm text-gray-500">
                        <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-3">Offer Title</th>
                                <th scope="col" class="px-6 py-3">Restaurant</th>
                                <th scope="col" class="px-6 py-3">Discount</th>
                                <th scope="col" class="px-6 py-3">Expiry Date</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="offersTableBody" class="divide-y divide-gray-200 bg-white">
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="hidden-el p-12 flex flex-col items-center justify-center text-center border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mt-2">No offers match criteria</h3>
                </div>
            </div>
        </main>
    </div>

    <!-- Create/Edit Offer Modal -->
    <div id="offerModal" class="relative z-40 hidden-el" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-content">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Create Offer</h3>
                        <button onclick="closeModal('offerModal')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    
                    <form id="offerForm" onsubmit="handleOfferSubmit(event)">
                        <input type="hidden" id="offerId">
                        <div class="px-6 py-5 space-y-4">
                            
                            <div>
                                <label for="offerTitleInput" class="block text-sm font-medium text-gray-700">Offer Title</label>
                                <input type="text" id="offerTitleInput" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-10 px-3 border" placeholder="e.g. 50% Off Dessert">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="offerDiscountInput" class="block text-sm font-medium text-gray-700">Discount %</label>
                                    <div class="relative mt-1 rounded-md shadow-sm">
                                        <input type="number" id="offerDiscountInput" min="1" max="100" required class="block w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary sm:text-sm h-10 px-3 border pr-8" placeholder="25">
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                            <span class="text-gray-500 sm:text-sm">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label for="offerExpiryInput" class="block text-sm font-medium text-gray-700">Expiry Date</label>
                                    <input type="date" id="offerExpiryInput" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-10 px-3 border">
                                </div>
                            </div>

                            <div>
                                <label for="offerRestaurantInput" class="block text-sm font-medium text-gray-700">Restaurant Selection</label>
                                <select id="offerRestaurantInput" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-10 px-3 border bg-white">
                                    <option value="All Restaurants">All Restaurants</option>
                                    <option value="Pizza Hut">Pizza Hut</option>
                                    <option value="Burger King">Burger King</option>
                                    <option value="Sushi Master">Sushi Master</option>
                                </select>
                            </div>

                            <div>
                                <label for="offerStatusInput" class="block text-sm font-medium text-gray-700">Status</label>
                                <select id="offerStatusInput" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-10 px-3 border bg-white">
                                    <option value="Active">Active</option>
                                    <option value="Expired">Expired</option>
                                </select>
                            </div>

                        </div>
                        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                            <button type="button" onclick="closeModal('offerModal')" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 focus:outline-none">Cancel</button>
                            <button type="submit" class="bg-primary border border-transparent text-white px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-primary_dark focus:outline-none">Save Offer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    <script src="{{ asset('modules/admin/js/offers.js') }}"></script>
</body>
</html>
