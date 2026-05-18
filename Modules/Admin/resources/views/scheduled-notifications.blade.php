<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheduled Notifications - Admin Dashboard</title>
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
                    <h2 class="text-2xl font-bold text-gray-900">{{ __('Scheduled Notifications') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ __('Notifications queued to be sent automatically at a future time.') }}</p>
                </div>
            </div>

            <!-- List Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto w-full">
                    <table class="w-full whitespace-nowrap text-left text-sm text-gray-500">
                        <thead
                            class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-3 w-1/4">{{ __('Scheduled Time') }}</th>
                                <th scope="col" class="px-6 py-3 w-1/4">{{ __('Title') }}</th>
                                <th scope="col" class="px-6 py-3 w-1/4">{{ __('Target') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Type') }}</th>
                                <th scope="col" class="px-6 py-3 text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="schedTableBody" class="divide-y divide-gray-200 bg-white">
                            @forelse ($notifications as $notification)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span
                                            class="bg-indigo-100 text-indigo-800 text-xs font-bold px-2.5 py-1 rounded inline-flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $notification->scheduled_at ? $notification->scheduled_at->format('Y-m-d h:i A') : 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $notification->title }}</div>
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
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-semibold text-gray-600 uppercase">{{ __('Push') }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <form action="{{ route('admin.notifications.destroy', $notification->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('{{ __('Cancel this scheduled notification?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-500 hover:text-red-700 font-medium text-sm">
                                                {{ __('Cancel') }}
                                            </button>
                                        </form>
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
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
</body>

</html>