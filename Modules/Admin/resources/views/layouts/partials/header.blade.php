<!-- Navbar -->
<header class="bg-white border-b border-gray-200 z-10 w-full shadow-sm">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        <!-- Hamburger Menu -->
        <div class="flex items-center flex-1">
            <button id="mobileMenuBtn" class="text-gray-500 focus:outline-none lg:hidden pl-1 pr-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>
            <!-- Search -->
            <div class="hidden md:flex w-full max-w-md ml-4">
                <div class="relative w-full text-gray-400 focus-within:text-gray-600">
                    <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none pl-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" placeholder="Search..."
                        class="w-full h-10 pl-10 pr-3 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all sm:text-sm">
                </div>
            </div>
        </div>

        <!-- Right Nav Items -->
        <div class="flex items-center space-x-4">
            <!-- Notification Dropdown -->
            <div class="relative inline-block text-left" id="notificationsDropdownWrapper">
                @php
                    $unreadCount = auth()->check() ? auth()->user()->unreadNotifications->count() : 0;
                    $latestNotifications = auth()->check() ? auth()->user()->unreadNotifications()->latest()->take(5)->get() : collect();
                @endphp
                <button id="notificationsDropdownBtn"
                    class="relative p-2 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                    @if($unreadCount > 0)
                        <span class="absolute top-0.5 right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white">
                            {{ $unreadCount }}
                        </span>
                    @endif
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                        </path>
                    </svg>
                </button>
                <div id="notificationsDropdownMenu"
                    class="hidden-el absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 py-1 z-30">
                    <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between">
                        <span class="font-semibold text-xs text-gray-800">{{ __('System Alerts') }}</span>
                        @if($unreadCount > 0)
                            <a href="{{ route('admin.notifications.inbox.mark-all-read') }}" class="text-[10px] text-indigo-600 hover:text-indigo-800 font-medium">{{ __('Mark all as read') }}</a>
                        @endif
                    </div>
                    <div class="max-h-64 overflow-y-auto divide-y divide-gray-50">
                        @forelse($latestNotifications as $notif)
                            @php
                                $notifData = $notif->data ?? [];
                                $source = $notifData['source'] ?? 'System';
                                $title = $notifData['title'] ?? 'Alert';
                                $body = $notifData['body'] ?? '';
                            @endphp
                            <a href="{{ route('admin.notifications.inbox.read', $notif->id) }}" class="block px-4 py-3 hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start mb-1 gap-2">
                                    <span class="font-semibold text-[11px] text-gray-900 truncate max-w-[150px]">{{ $title }}</span>
                                    <span class="text-[9px] text-gray-400 whitespace-nowrap">{{ $notif->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-[11px] text-gray-500 line-clamp-2 leading-relaxed">{{ $body }}</p>
                                <div class="mt-1">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-semibold 
                                        @if($source === 'Customer') bg-green-50 text-green-700
                                        @elseif($source === 'Driver') bg-blue-50 text-blue-700
                                        @elseif($source === 'Restaurant') bg-orange-50 text-orange-700
                                        @else bg-gray-50 text-gray-700
                                        @endif">
                                        {{ __($source) }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="px-4 py-6 text-center text-xs text-gray-400">
                                {{ __('No new alerts') }}
                            </div>
                        @endforelse
                    </div>
                    <div class="border-t border-gray-100">
                        <a href="{{ route('admin.notifications.inbox') }}" class="block text-center py-2 text-xs text-indigo-600 hover:text-indigo-800 font-semibold bg-gray-50 hover:bg-gray-100 rounded-b-lg">
                            {{ __('View All Alerts') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- ── Language Switcher ── -->
            <div class="relative" id="langSwitcherWrapper">
                <button id="langDropdownBtn"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:border-indigo-300 transition-all focus:outline-none"
                    aria-expanded="false" aria-haspopup="true">
                    <!-- Globe Icon -->
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9" />
                    </svg>
                    <span>{{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}</span>
                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div id="langDropdownMenu"
                    class="hidden-el absolute right-0 mt-2 w-36 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 py-1 z-30">
                    <a href="{{ route('lang.switch', 'en') }}"
                        class="flex items-center gap-2 px-4 py-2 text-sm {{ app()->getLocale() === 'en' ? 'text-indigo-600 font-semibold bg-indigo-50' : 'text-gray-700 hover:bg-gray-100' }}">
                        <span>🇬🇧</span> English
                    </a>
                    <a href="{{ route('lang.switch', 'ar') }}"
                        class="flex items-center gap-2 px-4 py-2 text-sm {{ app()->getLocale() === 'ar' ? 'text-indigo-600 font-semibold bg-indigo-50' : 'text-gray-700 hover:bg-gray-100' }}">
                        <span>🇸🇦</span> العربية
                    </a>
                </div>
            </div>

            <!-- Profile User Dropdown -->
            <div class="relative">
                <button id="profileDropdownBtn" class="flex items-center space-x-2 focus:outline-none">
                    <img class="w-8 h-8 rounded-full border-2 border-primary object-cover"
                        src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&background=4f46e5&color=fff">
                    <div class="hidden md:flex flex-col text-left">
                        <span class="font-medium text-sm text-gray-700">{{ Auth::user()->name ?? 'Admin' }}</span>
                        <span
                            class="text-xs text-gray-500">{{ Auth::check() && Auth::user()->roles->count() > 0 ? Auth::user()->roles->first()->name : 'Admin' }}</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="profileDropdownMenu"
                    class="hidden-el absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 z-20">
                    <a href="{{ route('admin.profile') }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('Your Profile') }}</a>
                    <a href="{{ route('admin.settings.index') }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('Settings') }}</a>
                    <div class="border-t border-gray-100"></div>
                    <form method="POST" action="{{ route('admin.logout') }}" id="logout-form">
                        @csrf
                        <button type="button" onclick="confirmLogout()"
                            class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">{{ __('Sign out') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    function confirmLogout() {
        if (confirm('هل أنت متأكد من تسجيل الخروج؟')) {
            document.getElementById('logout-form').submit();
        }
    }

    // Language Switcher Dropdown
    (function () {
        const btn = document.getElementById('langDropdownBtn');
        const menu = document.getElementById('langDropdownMenu');
        if (!btn || !menu) return;

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = !menu.classList.contains('hidden-el');
            // Close other dropdowns
            document.getElementById('notificationsDropdownMenu')?.classList.add('hidden-el');
            document.getElementById('profileDropdownMenu')?.classList.add('hidden-el');
            
            menu.classList.toggle('hidden-el', isOpen);
            btn.setAttribute('aria-expanded', String(!isOpen));
        });

        document.addEventListener('click', function () {
            menu.classList.add('hidden-el');
            btn.setAttribute('aria-expanded', 'false');
        });
    })();

    // Notifications Dropdown Toggle
    (function () {
        const btn = document.getElementById('notificationsDropdownBtn');
        const menu = document.getElementById('notificationsDropdownMenu');
        if (!btn || !menu) return;

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = !menu.classList.contains('hidden-el');
            // Close other dropdowns
            document.getElementById('langDropdownMenu')?.classList.add('hidden-el');
            document.getElementById('profileDropdownMenu')?.classList.add('hidden-el');
            
            menu.classList.toggle('hidden-el', isOpen);
            btn.setAttribute('aria-expanded', String(!isOpen));
        });

        document.addEventListener('click', function () {
            menu.classList.add('hidden-el');
            btn.setAttribute('aria-expanded', 'false');
        });
    })();

    // Profile Dropdown Toggle
    (function () {
        const btn = document.getElementById('profileDropdownBtn');
        const menu = document.getElementById('profileDropdownMenu');
        if (!btn || !menu) return;

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = !menu.classList.contains('hidden-el');
            // Close other dropdowns
            document.getElementById('langDropdownMenu')?.classList.add('hidden-el');
            document.getElementById('notificationsDropdownMenu')?.classList.add('hidden-el');
            
            menu.classList.toggle('hidden-el', isOpen);
            btn.setAttribute('aria-expanded', String(!isOpen));
        });

        document.addEventListener('click', function () {
            menu.classList.add('hidden-el');
            btn.setAttribute('aria-expanded', 'false');
        });
    })();
</script>