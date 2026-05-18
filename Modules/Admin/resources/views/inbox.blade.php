<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('System Alerts') }} - {{ __('Admin Dashboard') }}</title>
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

            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ __('System Alerts') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ __('Incoming system alerts, driver location changes, customer orders, and compliance messages.') }}
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.notifications.inbox.mark-all-read') }}"
                        class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors whitespace-nowrap">
                        <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ __('Mark all as read') }}
                    </a>
                </div>
            </div>

            <!-- List Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden opacity-90">
                <div class="overflow-x-auto w-full">
                    <table class="w-full whitespace-nowrap text-left text-sm text-gray-500">
                        <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-3 w-40">{{ __('Source') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Content') }}</th>
                                <th scope="col" class="px-6 py-3 w-48">{{ __('Received At') }}</th>
                                <th scope="col" class="px-6 py-3 w-32 text-right">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($notifications as $notification)
                                @php
                                    $notifData = $notification->data ?? [];
                                    $source = $notifData['source'] ?? 'System';
                                    $title = $notifData['title'] ?? 'Alert';
                                    $body = $notifData['body'] ?? '';
                                    $isRead = !is_null($notification->read_at);
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors {{ $isRead ? 'opacity-60 bg-gray-50/50' : '' }}">
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold 
                                            @if($source === 'Customer') bg-green-100 text-green-800
                                            @elseif($source === 'Driver') bg-blue-100 text-blue-800
                                            @elseif($source === 'Restaurant') bg-orange-100 text-orange-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ __($source) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-semibold text-gray-900">{{ $title }}</div>
                                        <div class="text-xs text-gray-500 mt-1 max-w-xl break-words leading-relaxed">
                                            {{ $body }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                                        {{ $notification->created_at ? $notification->created_at->diffForHumans() : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.notifications.inbox.read', $notification->id) }}"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg text-white bg-primary hover:bg-primary_dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                                            {{ __('View Action') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                        {{ __('No system alerts found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Section -->
                @if($notifications->hasPages())
                    <div class="border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- Confirmation & Mobile JS -->
    <script>
        document.getElementById('mobileMenuBtn')?.addEventListener('click', function () {
            document.getElementById('sidebar')?.classList.toggle('-translate-x-full');
            document.getElementById('sidebarBackdrop')?.classList.toggle('hidden-el');
        });
        document.getElementById('sidebarBackdrop')?.addEventListener('click', function () {
            document.getElementById('sidebar')?.classList.add('-translate-x-full');
            document.getElementById('sidebarBackdrop')?.classList.add('hidden-el');
        });
    </script>
</body>

</html>
