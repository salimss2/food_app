<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Admin Dashboard') }}</title>

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

    @stack('styles')
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

        .hidden-el, .hidden {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased overflow-hidden flex h-screen">

    <!-- Sidebar included here -->
    @include('admin::layouts.partials.sidebar')

    <!-- Layout Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300">

        <!-- Header included here -->
        @include('admin::layouts.partials.header')

        <!-- Main Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto w-full">
            @yield('content')
        </main>

    </div>

    <!-- Script tags -->
    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    
    <!-- Idempotent Global Dropdowns and Sidebar Toggle Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Global Sidebar Toggle
            document.addEventListener('click', (e) => {
                const mobileMenuBtn = e.target.closest('#mobileMenuBtn');
                const sidebarBackdrop = e.target.closest('#sidebarBackdrop');
                const sidebar = document.getElementById('sidebar');
                const backdropEl = document.getElementById('sidebarBackdrop');

                if (mobileMenuBtn && sidebar && backdropEl) {
                    sidebar.classList.remove('-translate-x-full');
                    backdropEl.classList.remove('hidden-el', 'hidden');
                } else if ((sidebarBackdrop || !e.target.closest('#sidebar')) && sidebar && backdropEl) {
                    // Close sidebar only if we click backdrop or click outside the sidebar (not mobile button)
                    if (!e.target.closest('#mobileMenuBtn')) {
                        sidebar.classList.add('-translate-x-full');
                        backdropEl.classList.add('hidden-el', 'hidden');
                    }
                }
            });

            // Global Dropdowns Toggle (Language, Notifications, Profile)
            document.addEventListener('click', (e) => {
                const dropdowns = [
                    { btnId: 'langDropdownBtn', menuId: 'langDropdownMenu' },
                    { btnId: 'notificationsDropdownBtn', menuId: 'notificationsDropdownMenu' },
                    { btnId: 'profileDropdownBtn', menuId: 'profileDropdownMenu' }
                ];

                let clickedDropdown = null;

                for (const d of dropdowns) {
                    const btn = document.getElementById(d.btnId);
                    const menu = document.getElementById(d.menuId);
                    if (!btn || !menu) continue;

                    if (e.target.closest(`#${d.btnId}`)) {
                        e.preventDefault();
                        e.stopPropagation();
                        clickedDropdown = d;
                        
                        const isCurrentlyHidden = menu.classList.contains('hidden-el') || menu.classList.contains('hidden');
                        if (isCurrentlyHidden) {
                            menu.classList.remove('hidden-el', 'hidden');
                            btn.setAttribute('aria-expanded', 'true');
                        } else {
                            menu.classList.add('hidden-el', 'hidden');
                            btn.setAttribute('aria-expanded', 'false');
                        }
                    }
                }

                // Close all other dropdowns when clicking anywhere else
                for (const d of dropdowns) {
                    if (clickedDropdown && clickedDropdown.btnId === d.btnId) continue;
                    const menu = document.getElementById(d.menuId);
                    const btn = document.getElementById(d.btnId);
                    
                    // Only close if we clicked outside this dropdown's button and menu
                    if (menu && !e.target.closest(`#${d.menuId}`)) {
                        menu.classList.add('hidden-el', 'hidden');
                        if (btn) btn.setAttribute('aria-expanded', 'false');
                    }
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>