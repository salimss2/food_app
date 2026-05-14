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
            <a href="{{ route('admin.notifications.index') }}"
                class="relative p-2 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                <span class="absolute top-1.5 right-1.5 block w-2 h-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                    </path>
                </svg>
            </a>

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
            menu.classList.toggle('hidden-el', isOpen);
            btn.setAttribute('aria-expanded', String(!isOpen));
        });

        document.addEventListener('click', function () {
            menu.classList.add('hidden-el');
            btn.setAttribute('aria-expanded', 'false');
        });
    })();
</script>