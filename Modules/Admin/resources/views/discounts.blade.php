<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discounts Management - Admin Dashboard</title>
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
                    <h2 class="text-2xl font-bold text-gray-900">Discount Codes</h2>
                    <p class="text-sm text-gray-500 mt-1">Generate and manage promotional codes used during checkout.</p>
                </div>
                <div class="flex space-x-2">
                    <select id="discountFilter" class="block w-full rounded-lg border-gray-300 py-2 pl-3 pr-10 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary shadow-sm bg-white" onchange="renderTable()">
                        <option value="All">All Types</option>
                        <option value="Percentage">Percentage</option>
                        <option value="Fixed">Fixed Amount</option>
                    </select>
                    <button onclick="openModal()" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary_dark focus:outline-none transition-colors whitespace-nowrap">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Create Code
                    </button>
                </div>
            </div>

            <!-- List Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto w-full">
                    <table class="w-full whitespace-nowrap text-left text-sm text-gray-500">
                        <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-3">Code</th>
                                <th scope="col" class="px-6 py-3">Value</th>
                                <th scope="col" class="px-6 py-3">Conditions</th>
                                <th scope="col" class="px-6 py-3">Usage</th>
                                <th scope="col" class="px-6 py-3">Expiry Date</th>
                                <th scope="col" class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="discountsTableBody" class="divide-y divide-gray-200 bg-white">
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="hidden-el p-12 flex flex-col items-center justify-center text-center border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mt-2">No discount codes found</h3>
                </div>
            </div>
        </main>
    </div>

    <!-- Create/Edit Discount Modal -->
    <div id="discountModal" class="relative z-40 hidden-el" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-content">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Create Discount Code</h3>
                        <button onclick="closeModal('discountModal')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    
                    <form id="discountForm" onsubmit="handleDiscountSubmit(event)">
                        <input type="hidden" id="discountId">
                        <div class="px-6 py-5 space-y-4">
                            
                            <!-- Basic details -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="codeTitleInput" class="block text-sm font-medium text-gray-700">Code (Alphanumeric)</label>
                                    <input type="text" id="codeTitleInput" required style="text-transform:uppercase" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-10 px-3 border" placeholder="WELCOME20">
                                </div>
                                <div>
                                    <label for="expiryInput" class="block text-sm font-medium text-gray-700">Expiry Date</label>
                                    <input type="date" id="expiryInput" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-10 px-3 border">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="typeInput" class="block text-sm font-medium text-gray-700">Discount Type</label>
                                    <select id="typeInput" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-10 px-3 border bg-white">
                                        <option value="Percentage">Percentage (%)</option>
                                        <option value="Fixed">Fixed Amount ($)</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="valueInput" class="block text-sm font-medium text-gray-700">Discount Value</label>
                                    <input type="number" id="valueInput" required min="1" step="0.1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-10 px-3 border" placeholder="20">
                                </div>
                            </div>

                            <hr class="border-gray-200 my-2">

                            <!-- Conditions -->
                            <div>
                                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Conditions & Limits</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="minOrderInput" class="block text-sm font-medium text-gray-700">Min. Order Amount ($)</label>
                                        <input type="number" id="minOrderInput" required min="0" step="0.5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-10 px-3 border" placeholder="15.00">
                                    </div>
                                    <div>
                                        <label for="limitInput" class="block text-sm font-medium text-gray-700">Max Usages (Global)</label>
                                        <input type="number" id="limitInput" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-10 px-3 border" placeholder="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                            <button type="button" onclick="closeModal('discountModal')" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 focus:outline-none">Cancel</button>
                            <button type="submit" class="bg-primary border border-transparent text-white px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-primary_dark focus:outline-none">Save Code</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    <script src="{{ asset('modules/admin/js/discounts.js') }}"></script>
</body>
</html>
