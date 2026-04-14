<header class="bg-white shadow-sm ring-1 ring-gray-200 z-10 w-full">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center flex-1">
            <button id="mobileMenuBtn" class="text-gray-500 focus:outline-none lg:hidden pl-1 pr-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>
            <div class="hidden md:flex w-full max-w-md ml-4">
                <div class="relative w-full text-gray-400 focus-within:text-gray-600">
                    <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none pl-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" placeholder="Global search..."
                        class="w-full h-10 pl-10 pr-3 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white focus:border-transparent transition-all sm:text-sm">
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-4">
            <button class="relative p-2 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                <span class="absolute top-1.5 right-1.5 block w-2 h-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                    </path>
                </svg>
            </button>
            <div class="relative">
                <button id="profileDropdownBtn" class="flex items-center space-x-2 focus:outline-none">
                    {{-- جلب بيانات الأدمن المسجل حالياً باستخدام الحارس (Guard) المناسب --}}
                    {{-- جلب بيانات المستخدم أو الأدمن المسجل حالياً --}}
                    @php
                        $admin = auth()->user();
                    @endphp

                    <img class="w-8 h-8 rounded-full border-2 border-primary object-cover"
                        src="https://ui-avatars.com/api/?name={{ urlencode($admin ? $admin->name : 'Admin') }}&background=4f46e5&color=fff"
                        alt="Admin avatar">

                    <span class="hidden md:block font-medium text-sm text-gray-700">
                        {{ $admin ? $admin->name : 'Admin' }}
                    </span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="profileDropdownMenu"
                    class="hidden-el absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 z-20">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Your Profile</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                    <div class="border-t border-gray-100"></div>

                    {{-- تحويل تسجيل الخروج إلى Form كما يتطلب Laravel للحماية من ثغرات CSRF --}}
                    <form method="POST" action="{{ route('admin.logout') ?? '#' }}">
                        @csrf
                        <button type="submit"
                            class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
