<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .hidden-el { display: none; }
        .text-primary { color: #4f46e5; }
        .bg-primary { background-color: #4f46e5; }
        .border-primary { border-color: #4f46e5; }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased overflow-hidden flex h-screen">

    @include('layouts.sidebar')

    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300">
        
        @include('layouts.navbar')

        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto w-full">
            @yield('content')
        </main>

    </div>

    <script>
        // Profile Dropdown
        const profileBtn = document.getElementById('profileDropdownBtn');
        const profileMenu = document.getElementById('profileDropdownMenu');
        
        if(profileBtn) {
            profileBtn.addEventListener('click', () => {
                profileMenu.classList.toggle('hidden');
            });
        }

        // Mobile Sidebar
        const mobileBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');

        if(mobileBtn) {
            mobileBtn.addEventListener('click', () => {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden-el');
            });
        }

        if(backdrop) {
            backdrop.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden-el');
            });
        }
    </script>

    @stack('scripts')
</body>
</html>