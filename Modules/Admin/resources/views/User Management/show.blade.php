<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - {{ $user->name }}</title>
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
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased overflow-hidden flex h-screen">

    {{-- Sidebar --}}
    <div id="sidebarBackdrop" class="fixed inset-0 z-20 bg-gray-900 bg-opacity-50 lg:hidden hidden"></div>
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-gray-200 transition-transform duration-300 transform -translate-x-full lg:translate-x-0 lg:static lg:inset-0 lg:flex lg:flex-col shadow-sm">
        <div class="flex items-center justify-center h-16 border-b border-gray-200 px-6">
            <h1 class="text-xl font-bold text-gray-900 flex items-center space-x-2">
                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <span>AdminPanel</span>
            </h1>
        </div>
        <div class="overflow-y-auto overflow-x-hidden flex-grow shadow-inner">
            <ul class="flex flex-col py-4 space-y-1 px-3 mb-10">
                <li class="px-2"><div class="text-xs uppercase font-semibold text-gray-400 tracking-wider mb-2">Menu</div></li>
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent px-6 group transition-colors">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span class="ml-3 font-medium text-sm">Dashboard</span>
                    </a>
                </li>
                <li class="px-2 mt-4"><div class="text-xs uppercase font-semibold text-gray-400 tracking-wider mb-2">Users & Roles</div></li>
                <li>
                    <a href="{{ route('admin.users.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-primary bg-indigo-50 border-l-4 border-primary px-6 rounded-r-lg group transition-colors">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span class="ml-3 font-medium text-sm">Users Collection</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300">
        {{-- Navbar --}}
        <header class="bg-white shadow-sm ring-1 ring-gray-200 z-10">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center flex-1">
                    <button id="mobileMenuBtn" class="text-gray-500 focus:outline-none lg:hidden pl-1 pr-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    {{-- Breadcrumb --}}
                    <nav class="flex items-center space-x-2 text-sm text-gray-500">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <a href="{{ route('admin.users.index') }}" class="hover:text-primary transition-colors">Users</a>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <span class="font-medium text-gray-800">{{ $user->name }}</span>
                    </nav>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button id="profileDropdownBtn" class="flex items-center space-x-2 focus:outline-none">
                            <img class="w-8 h-8 rounded-full border-2 border-primary object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=4f46e5&color=fff" alt="Admin">
                            <span class="hidden md:block font-medium text-sm text-gray-700">{{ auth()->user()->name ?? 'Admin' }}</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="profileDropdownMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 z-20">
                            <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Your Profile</a>
                            <div class="border-t border-gray-100"></div>
                            <form method="POST" action="{{ route('admin.logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Sign out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success'))
        <div id="toastSuccess" class="fixed top-5 right-5 z-50 flex items-center gap-3 bg-white border border-green-200 shadow-lg rounded-xl px-5 py-4 min-w-[280px]" role="alert">
            <div class="flex-shrink-0 w-9 h-9 rounded-full bg-green-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-green-800">Success</p>
                <p class="text-xs text-green-600 mt-0.5">{{ session('success') }}</p>
            </div>
            <button onclick="document.getElementById('toastSuccess').remove()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <script>setTimeout(() => { const t = document.getElementById('toastSuccess'); if(t) t.remove(); }, 4000);</script>
        @endif

        {{-- Main Content --}}
        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto w-full">

            {{-- Page Header --}}
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-3 sm:space-y-0">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">User Profile</h2>
                    <p class="text-sm text-gray-500 mt-1">Viewing details for {{ $user->name }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Back to Users
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- LEFT: Profile Card --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- Avatar & Basic Info --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                        <div class="relative inline-block">
                            <img class="w-24 h-24 rounded-full border-4 border-primary/20 object-cover mx-auto shadow"
                                 src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=4f46e5&color=fff&size=200" alt="{{ $user->name }}">
                            <span class="absolute bottom-0 right-0 w-5 h-5 rounded-full border-2 border-white {{ strtolower($user->status) === 'active' ? 'bg-green-400' : 'bg-red-400' }}"></span>
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-gray-900">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $user->email }}</p>

                        <div class="mt-3 flex justify-center gap-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                {{ $user->roles->first()?->name ?? 'Customer' }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ strtolower($user->status) === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $user->status ?? 'Active' }}
                            </span>
                        </div>

                        <div class="mt-5 text-sm text-gray-500 space-y-1">
                            <p class="flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                {{ $user->phone ?? 'N/A' }}
                            </p>
                            <p class="flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Joined {{ $user->created_at->format('d M, Y') }}
                            </p>
                        </div>
                    </div>

                    {{-- Quick Stats --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 divide-y divide-gray-100">
                        <div class="px-5 py-3">
                            <p class="text-xs uppercase font-semibold text-gray-400 tracking-wider">Account Summary</p>
                        </div>
                        <div class="flex items-center justify-between px-5 py-3">
                            <span class="text-sm text-gray-600">Total Orders</span>
                            <span class="text-sm font-semibold text-gray-900 bg-indigo-50 text-indigo-700 px-2.5 py-0.5 rounded-full">0</span>
                        </div>
                        <div class="flex items-center justify-between px-5 py-3">
                            <span class="text-sm text-gray-600">Total Spent</span>
                            <span class="text-sm font-semibold text-green-700">$0.00</span>
                        </div>
                        <div class="flex items-center justify-between px-5 py-3">
                            <span class="text-sm text-gray-600">Reviews</span>
                            <span class="text-sm font-semibold text-yellow-700">0</span>
                        </div>
                        <div class="flex items-center justify-between px-5 py-3">
                            <span class="text-sm text-gray-600">Member Since</span>
                            <span class="text-sm font-medium text-gray-700">{{ $user->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Personal Information & Activity --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Personal Info --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h4 class="text-base font-semibold text-gray-900">Personal Information</h4>
                        </div>
                        <div class="divide-y divide-gray-50">
                            <div class="grid grid-cols-3 gap-4 px-6 py-4">
                                <span class="text-sm text-gray-500 font-medium">Full Name</span>
                                <span class="col-span-2 text-sm text-gray-900 font-semibold">{{ $user->name }}</span>
                            </div>
                            <div class="grid grid-cols-3 gap-4 px-6 py-4">
                                <span class="text-sm text-gray-500 font-medium">Email Address</span>
                                <span class="col-span-2 text-sm text-gray-900">{{ $user->email }}</span>
                            </div>
                            <div class="grid grid-cols-3 gap-4 px-6 py-4">
                                <span class="text-sm text-gray-500 font-medium">Phone Number</span>
                                <span class="col-span-2 text-sm text-gray-900">{{ $user->phone ?? '—' }}</span>
                            </div>
                            <div class="grid grid-cols-3 gap-4 px-6 py-4">
                                <span class="text-sm text-gray-500 font-medium">Role</span>
                                <span class="col-span-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                        {{ $user->roles->first()?->name ?? 'Customer' }}
                                    </span>
                                </span>
                            </div>
                            <div class="grid grid-cols-3 gap-4 px-6 py-4">
                                <span class="text-sm text-gray-500 font-medium">Status</span>
                                <span class="col-span-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ strtolower($user->status) === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $user->status ?? 'Active' }}
                                    </span>
                                </span>
                            </div>
                            <div class="grid grid-cols-3 gap-4 px-6 py-4">
                                <span class="text-sm text-gray-500 font-medium">Registered</span>
                                <span class="col-span-2 text-sm text-gray-900">{{ $user->created_at->format('d M Y — H:i') }}</span>
                            </div>
                            <div class="grid grid-cols-3 gap-4 px-6 py-4">
                                <span class="text-sm text-gray-500 font-medium">Last Updated</span>
                                <span class="col-span-2 text-sm text-gray-900">{{ $user->updated_at->format('d M Y — H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Recent Orders Placeholder --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h4 class="text-base font-semibold text-gray-900">Recent Orders</h4>
                            <span class="text-xs text-gray-400">Coming soon</span>
                        </div>
                        <div class="flex flex-col items-center justify-center py-16 text-center px-6">
                            <svg class="w-12 h-12 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            <p class="text-sm font-medium text-gray-400">No orders found</p>
                            <p class="text-xs text-gray-300 mt-1">This user hasn't placed any orders yet.</p>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    <script>
        // Sidebar & Dropdown
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const profileDropdownBtn = document.getElementById('profileDropdownBtn');
        const profileDropdownMenu = document.getElementById('profileDropdownMenu');
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', () => {
                sidebar.classList.remove('-translate-x-full');
                sidebarBackdrop.classList.remove('hidden');
            });
        }
        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
                sidebarBackdrop.classList.add('hidden');
            });
        }
        if (profileDropdownBtn) {
            profileDropdownBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                profileDropdownMenu.classList.toggle('hidden');
            });
        }
        document.addEventListener('click', () => {
            if (profileDropdownMenu && !profileDropdownMenu.classList.contains('hidden')) {
                profileDropdownMenu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>