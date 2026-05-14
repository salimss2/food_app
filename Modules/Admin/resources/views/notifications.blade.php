<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications Hub - Admin Dashboard</title>
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
                    <h2 class="text-2xl font-bold text-gray-900">{{ __('Broadcast Notifications') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ __('Send push notifications or emails to specific user groups.') }}</p>
                </div>
                <div class="flex space-x-2">
                    <select id="notifFilter"
                        class="block w-full rounded-lg border-gray-300 py-2 ps-3 pe-10 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary shadow-sm bg-white"
                        onchange="renderNotifications()">
                        <option value="All">{{ __('All Targets') }}</option>
                        <option value="Users">{{ __('Users') }}</option>
                        <option value="Drivers">{{ __('Drivers') }}</option>
                        <option value="Restaurants">{{ __('Restaurants') }}</option>
                    </select>
                    <button onclick="document.getElementById('sendNotifModal').classList.remove('hidden-el')"
                        class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary_dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors whitespace-nowrap">
                        <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        {{ __('New Notification') }}
                    </button>
                </div>
            </div>

            <!-- Toast Alert -->
            <div id="toast"
                class="hidden-el fixed bottom-4 right-4 z-50 rounded-md bg-green-50 p-4 shadow-lg border border-green-200 transition-opacity duration-300">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ms-3">
                        <p class="text-sm font-medium text-green-800" id="toastMessage">
                            {{ __('Notification sent successfully.') }}</p>
                    </div>
                </div>
            </div>

            <!-- List Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto w-full">
                    <table class="w-full whitespace-nowrap text-left text-sm text-gray-500">
                        <thead
                            class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-3 w-1/4">{{ __('Title / Date') }}</th>
                                <th scope="col" class="px-6 py-3 w-1/2">{{ __('Message Preview') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Target') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Type') }}</th>
                                <th scope="col" class="px-6 py-3 text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="notifTableBody" class="divide-y divide-gray-200 bg-white">
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div id="emptyState"
                    class="hidden-el p-12 flex flex-col items-center justify-center text-center border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mt-2">{{ __('No active broadcasts') }}</h3>
                </div>
            </div>
        </main>
    </div>

    <!-- Send Notification Modal -->
    <div id="sendNotifModal" class="relative z-40 hidden-el" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl modal-content">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Compose Notification') }}</h3>
                        <button onclick="document.getElementById('sendNotifModal').classList.add('hidden-el')"
                            class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form id="notifForm" onsubmit="handleSendNotification(event)">
                        <div class="px-6 py-5 space-y-4">

                            <div>
                                <label for="notifTitle"
                                    class="block text-sm font-medium text-gray-700">{{ __('Notification Title') }}</label>
                                <input type="text" id="notifTitle" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-10 ps-3 pe-3 border"
                                    placeholder="{{ __('e.g. Free Delivery Weekend!') }}">
                            </div>

                            <div>
                                <label for="notifTarget"
                                    class="block text-sm font-medium text-gray-700">{{ __('Target Audience') }}</label>
                                <select id="notifTarget" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-10 ps-3 pe-8 border bg-white">
                                    <option value="All">{{ __('All Targets') }}</option>
                                    <option value="Users">{{ __('Users') }}</option>
                                    <option value="Drivers">{{ __('Drivers') }}</option>
                                    <option value="Restaurants">{{ __('Restaurants') }}</option>
                                </select>
                            </div>

                            <div>
                                <label for="notifType"
                                    class="block text-sm font-medium text-gray-700">{{ __('Notification Type') }}</label>
                                <div class="mt-2 flex space-x-6">
                                    <div class="flex items-center">
                                        <input id="typePush" name="notifType" type="radio" value="Push" checked
                                            class="focus:ring-primary h-4 w-4 text-primary border-gray-300">
                                        <label for="typePush"
                                            class="ms-2 block text-sm text-gray-700">{{ __('Push Notification') }}</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="typeEmail" name="notifType" type="radio" value="Email"
                                            class="focus:ring-primary h-4 w-4 text-primary border-gray-300">
                                        <label for="typeEmail"
                                            class="ms-2 block text-sm text-gray-700">{{ __('Email') }}</label>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="notifMessage"
                                    class="block text-sm font-medium text-gray-700">{{ __('Message Body') }}</label>
                                <textarea id="notifMessage" rows="4" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-3 border"
                                    placeholder="{{ __('Type your message here...') }}"></textarea>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ __('Keep push notifications under 150 characters for best visibility.') }}</p>
                            </div>

                        </div>
                        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                            <button type="button"
                                onclick="document.getElementById('sendNotifModal').classList.add('hidden-el')"
                                class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">{{ __('Cancel') }}</button>
                            <button type="submit"
                                class="bg-primary border border-transparent text-white px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-primary_dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">{{ __('Send Now') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    <script src="{{ asset('modules/admin/js/notifications.js') }}"></script>
</body>

</html>