<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - Admin Dashboard</title>
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
        <main class="flex-1 overflow-y-auto w-full bg-gray-50 flex flex-col">

            <!-- Sub Navigation Tabs for Settings using DOM toggling -->
            <div class="bg-white border-b border-gray-200 px-4 sm:px-6 lg:px-8">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button onclick="switchTab('app')" id="tab-app"
                        class="border-primary text-primary whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">{{ __('App Config') }}</button>
                    <button onclick="switchTab('notif')" id="tab-notif"
                        class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">{{ __('Notifications Config') }}</button>
                    <button onclick="switchTab('legal')" id="tab-legal"
                        class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">{{ __('Terms & Conditions') }}</button>
                    <button onclick="switchTab('privacy')" id="tab-privacy"
                        class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">{{ __('Privacy Policy') }}</button>
                </nav>
            </div>

            <!-- Toast Alert -->
            <div id="toast"
                class="hidden-el fixed bottom-4 right-4 z-50 rounded-md bg-green-50 p-4 shadow-lg border border-green-200 transition-opacity duration-300">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400 border-green-200" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ms-3">
                        <p class="text-sm font-medium text-green-800" id="toastMessage">{{ __('Success') }}</p>
                    </div>
                </div>
            </div>

            <!-- Tab Content Wrapper -->
            <div class="p-4 sm:p-6 lg:p-8 flex-1">

                <!-- TAB 1: App Config -->
                <div id="tab-app" class="max-w-4xl mx-auto space-y-6">
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">{{ __('Application Configuration') }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ __('Global string parameters powering the front-end user experience.') }}
                            </p>
                        </div>
                    </div>
                    <div
                        class="bg-white shadow-sm ring-1 ring-gray-200 rounded-xl overflow-hidden divide-y divide-gray-100">
                        <!-- Key/Value Row -->
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="w-1/2">
                                <h4 class="text-sm font-semibold text-gray-900">App Name</h4>
                                <p class="text-xs text-gray-500">Displayed on splash screens and emails.</p>
                            </div>
                            <div class="w-1/3 text-right font-medium text-gray-700" id="val-appName">
                                FoodDelivery Pro
                            </div>
                            <button onclick="openEditModal('App Name', 'val-appName')"
                                class="ml-4 text-indigo-600 hover:text-indigo-900 text-sm font-bold">Edit</button>
                        </div>

                        <!-- Key/Value Row -->
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="w-1/2">
                                <h4 class="text-sm font-semibold text-gray-900">Support Email</h4>
                                <p class="text-xs text-gray-500">Default fallback email for user dispute tickets.</p>
                            </div>
                            <div class="w-1/3 text-right font-medium text-gray-700" id="val-supEmail">
                                help@fooddelivery.app
                            </div>
                            <button onclick="openEditModal('Support Email', 'val-supEmail')"
                                class="ml-4 text-indigo-600 hover:text-indigo-900 text-sm font-bold">Edit</button>
                        </div>

                        <!-- Key/Value Row -->
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="w-1/2">
                                <h4 class="text-sm font-semibold text-gray-900">Global Currency Symbol</h4>
                                <p class="text-xs text-gray-500">Injected into prefix span classes dynamically.</p>
                            </div>
                            <div class="w-1/3 text-right font-medium text-gray-700" id="val-currency">
                                $
                            </div>
                            <button onclick="openEditModal('Global Currency Symbol', 'val-currency')"
                                class="ml-4 text-indigo-600 hover:text-indigo-900 text-sm font-bold">Edit</button>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: Notifications Config -->
                <div id="tab-notif" class="max-w-4xl mx-auto space-y-6 hidden-el">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-base font-bold text-gray-900">{{ __('API Notifications Config') }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">{{ __('SMTP and Auth keys for OneSignal Push hooks.') }}
                        </p>
                    </div>

                    <div
                        class="bg-white shadow-sm ring-1 ring-gray-200 rounded-xl overflow-hidden divide-y divide-gray-100">
                        <!-- Key/Value Row -->
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="w-1/2">
                                <h4 class="text-sm font-semibold text-gray-900">OneSignal App ID</h4>
                                <p class="text-xs text-gray-500">Required payload for push broadcasts to phones.</p>
                            </div>
                            <div class="w-1/3 text-right font-medium text-gray-500 italic" id="val-oneSigId">
                                d3f4j-****-****
                            </div>
                            <button onclick="openEditModal('OneSignal App ID', 'val-oneSigId')"
                                class="ml-4 text-indigo-600 hover:text-indigo-900 text-sm font-bold">Edit</button>
                        </div>

                        <!-- Key/Value Row -->
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="w-1/2">
                                <h4 class="text-sm font-semibold text-gray-900">SMTP Host (SendGrid)</h4>
                                <p class="text-xs text-gray-500">Mailing server endpoint URL.</p>
                            </div>
                            <div class="w-1/3 text-right font-medium text-gray-700" id="val-smtp">
                                smtp.sendgrid.net
                            </div>
                            <button onclick="openEditModal('SMTP Host (SendGrid)', 'val-smtp')"
                                class="ml-4 text-indigo-600 hover:text-indigo-900 text-sm font-bold">Edit</button>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: Terms & Conditions -->
                <div id="tab-terms" class="max-w-4xl mx-auto space-y-6 hidden-el">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">{{ __('Terms & Conditions') }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ __('Legal formatting rendered dynamically in the mobile app web-view.') }}
                            </p>
                        </div>
                        <button onclick="openLegalModal('terms')"
                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all">
                            <svg class="w-3.5 h-3.5 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            {{ __('Edit Terms') }}
                        </button>
                    </div>

                    <div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-xl overflow-hidden p-6 text-sm text-gray-600 leading-relaxed font-sans"
                        style="white-space: pre-wrap;" id="val-termsBody">
                        Welcome to FoodDelivery Pro.

                        By accessing this platform, you agree to comply with our binding terms...

                        1. Order Liabilities:
                        The platform is not responsible for food temperature upon arrival. Refunds are strictly mediated
                        by our complaints department and subject to an internal 24 hour review cycle.

                        2. Driver Guidelines:
                        Drivers must retain an active background check within the past 12 months.
                    </div>
                </div>

                <!-- TAB 4: Privacy Policy -->
                <div id="tab-privacy" class="max-w-4xl mx-auto space-y-6 hidden-el">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">{{ __('System Settings') }}</h2>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ __('Manage application configuration and platform legal documents.') }}
                        </p>
                    </div>
                    <button onclick="openTextModal('Privacy Policy', 'val-privBody')"
                        class="bg-primary text-white text-sm font-bold py-2 px-4 rounded shadow-sm hover:bg-primary_dark transition-colors">Edit
                        Policy</button>
                </div>

                <div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-xl overflow-hidden p-6 text-sm text-gray-600 leading-relaxed font-sans"
                    style="white-space: pre-wrap;" id="val-privBody">
                    Data Request Limits:
                    We retain transactional delivery mapping metadata for up to 90 days.

                    Personally Identifiable Information revolves exclusively around IP Tracking for security and Payment
                    gateways natively encrypted via Stripe Tokens. We literally store zero raw cards in our active db
                    schemas.
                </div>
            </div>

    </div>
    </main>
    </div>

    <!-- Edit Short String Modal -->
    <div id="editStringModal" class="relative z-40 hidden-el" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-xl bg-white text-start shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md modal-content">
                    <form id="editForm" onsubmit="handleSaveSetting(event)">
                        <div class="bg-white px-6 pt-5 pb-4 border-b border-gray-100">
                            <h3 class="text-lg font-bold text-gray-900" id="modalTitle">{{ __('Update Setting') }}</h3>
                        </div>
                        <div class="px-6 py-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">{{ __('Edit Key') }}</label>
                                <input type="text" id="settingKey" readonly
                                    class="mt-1 block w-full border-0 bg-gray-50 text-gray-400 sm:text-sm h-10 px-3 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('New Value') }}</label>
                                <input type="text" id="settingValue" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-10 ps-3 pe-3 border">
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-3 flex flex-row-reverse space-x-reverse space-x-3">
                            <button type="submit"
                                class="inline-flex justify-center rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary_dark sm:ms-3">{{ __('Update Setting') }}</button>
                            <button type="button" onclick="closeModal('editModal')"
                                class="inline-flex justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">{{ __('Cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Long Text block Modal -->
    <div id="editTextModal" class="relative z-40 hidden-el" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="px-6 py-5">
                    <label for="textValInput" class="block text-sm font-medium text-gray-700 mb-1">Markdown / Plaintext
                        Supported</label>
                    <textarea id="textValInput" required rows="12"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-3 border border-gray-200"></textarea>
                </div>
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" onclick="closeModals()"
                        class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">Cancel</button>
                    <button type="submit"
                        class="bg-primary text-white px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-primary_dark transition-colors">Publish
                        Document</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    <script src="{{ asset('modules/admin/js/settings.js') }}"></script>
</body>

</html>