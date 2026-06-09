<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification History - Admin Dashboard</title>
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

            <!-- Flash Message Alerts -->
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4 border border-green-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ms-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-md bg-red-50 p-4 border border-red-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ms-3">
                            <h3 class="text-sm font-medium text-red-800">{{ __('Please correct the following errors:') }}
                            </h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div
                class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ __('Notification History') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ __('Archive of all past notifications and their delivery statistics.') }}
                    </p>
                </div>
                <div class="flex space-x-2">
                    <input type="text" id="histSearch" placeholder="{{ __('Search history...') }}"
                        class="block w-64 rounded-lg border-gray-300 py-2 ps-3 pe-3 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary shadow-sm bg-white border border-gray-300">
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

            <!-- List Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden opacity-90">
                <div class="overflow-x-auto w-full">
                    <table class="w-full whitespace-nowrap text-left text-sm text-gray-500">
                        <thead
                            class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-3 w-1/4">{{ __('Date Sent') }}</th>
                                <th scope="col" class="px-6 py-3 w-1/3">{{ __('Title') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Audience') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Status') }}</th>
                                <th scope="col" class="px-6 py-3 text-right">{{ __('Delivered To') }}</th>
                            </tr>
                        </thead>
                        <tbody id="histTableBody" class="divide-y divide-gray-200 bg-white">
                            @forelse ($notifications as $notification)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                        {{ $notification->created_at ? $notification->created_at->format('Y-m-d h:i A') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-semibold text-gray-900">{{ $notification->title }}</div>
                                        <div class="text-xs text-gray-500 mt-1 max-w-md break-words">
                                            {{ $notification->body }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($notification->target_role === 'Restaurant Admin')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                {{ __('Restaurants') }}
                                            </span>
                                        @elseif($notification->target_role === 'Driver')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ __('Drivers') }}
                                            </span>
                                        @elseif($notification->target_role === 'Customer')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ __('Users') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ __('All Targets') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ __('Sent') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap text-sm font-bold text-gray-600">
                                        {{ __('Delivered') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        {{ __('No notifications found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Placeholder -->
                <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">{{ __('Showing') }} <span class="font-medium">1</span>
                                {{ __('to') }} <span class="font-medium"
                                    id="histCount">{{ count($notifications) }}</span> {{ __('results') }}</p>
                        </div>
                        <div>
                            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                <a href="#"
                                    class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20"><span
                                        class="sr-only">Previous</span>&laquo;</a>
                                <a href="#" aria-current="page"
                                    class="relative z-10 inline-flex items-center bg-primary px-4 py-2 text-sm font-semibold text-white focus:z-20">1</a>
                                <a href="#"
                                    class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20"><span
                                        class="sr-only">Next</span>&raquo;</a>
                            </nav>
                        </div>
                    </div>
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

                    <form id="notifForm" action="{{ route('admin.notifications.store') }}" method="POST">
                        @csrf
                        <div class="px-6 py-5 space-y-4">

                            <div>
                                <label for="notifTitle"
                                    class="block text-sm font-medium text-gray-700">{{ __('Notification Title') }}</label>
                                <input type="text" id="notifTitle" name="title" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-10 ps-3 pe-3 border"
                                    placeholder="{{ __('e.g. Free Delivery Weekend!') }}" value="{{ old('title') }}">
                            </div>

                            <div>
                                <label for="notifTarget"
                                    class="block text-sm font-medium text-gray-700">{{ __('Target Audience') }}</label>
                                <select id="notifTarget" name="target_role" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-10 ps-3 pe-8 border bg-white">
                                    <option value="all" {{ old('target_role') == 'all' ? 'selected' : '' }}>
                                        {{ __('All Targets') }}
                                    </option>
                                    <option value="Customer" {{ old('target_role') == 'Customer' ? 'selected' : '' }}>
                                        {{ __('Users') }}
                                    </option>
                                    <option value="Driver" {{ old('target_role') == 'Driver' ? 'selected' : '' }}>
                                        {{ __('Drivers') }}
                                    </option>
                                    <option value="Restaurant Admin" {{ old('target_role') == 'Restaurant Admin' ? 'selected' : '' }}>
                                        {{ __('Restaurants') }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="notifScheduledAt"
                                    class="block text-sm font-medium text-gray-700">{{ __('Scheduled At (Optional)') }}</label>
                                <input type="datetime-local" id="notifScheduledAt" name="scheduled_at"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-10 ps-3 pe-3 border w-full"
                                    value="{{ old('scheduled_at') }}">
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ __('Leave blank to send immediately.') }}
                                </p>
                            </div>


                            <div>
                                <label for="notifMessage"
                                    class="block text-sm font-medium text-gray-700">{{ __('Message Body') }}</label>
                                <textarea id="notifMessage" name="body" rows="4" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-3 border"
                                    placeholder="{{ __('Type your message here...') }}">{{ old('body') }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ __('Keep push notifications under 150 characters for best visibility.') }}
                                </p>
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
    <script>
        document.getElementById('histSearch').addEventListener('input', function () {
            let query = this.value.toLowerCase();
            let rows = document.querySelectorAll('#histTableBody tr');
            let visibleCount = 0;
            rows.forEach(row => {
                // If it is the empty state row, skip it
                if (row.cells.length === 1 && row.cells[0].colSpan === 5) return;

                let text = row.innerText.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            let countEl = document.getElementById('histCount');
            if (countEl) {
                countEl.innerText = visibleCount;
            }
        });
    </script>
</body>

</html>