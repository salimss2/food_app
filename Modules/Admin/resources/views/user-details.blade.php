<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user->name }} — User Details</title>
    <!-- Tailwind config MUST come before the CDN script -->
    <script>
        tailwind = {
            config: {
                theme: {
                    extend: {
                        colors: { primary: '#4f46e5', primary_dark: '#4338ca' },
                        fontFamily: { sans: ['Inter', 'sans-serif'] }
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('modules/admin/css/app.css') }}">
    <style>
        /* Layout reset — ensures sidebar+main fill viewport exactly like users.blade.php */
        html,
        body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            overflow: hidden;
        }

        #sidebar {
            flex-shrink: 0;
        }

        .main-area {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .main-content {
            flex: 1;
            overflow-y: auto;
        }

        /* Tabs */
        .tab-btn.active {
            color: #4f46e5;
            border-bottom: 2px solid #4f46e5;
        }

        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
        }
    </style>
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

<body class="bg-gray-50 text-gray-800 font-sans antialiased">

    <!-- ══════════════════════════════════════════════════ -->
    <!-- SIDEBAR (copied 1:1 from users.blade.php)         -->
    <!-- ══════════════════════════════════════════════════ -->
    @include('admin::layouts.partials.sidebar')

    <!-- MAIN AREA -->
    <div class="main-area">

        <!-- Navbar (identical to users.blade.php) -->
        @include('admin::layouts.partials.header')

        <!-- MAIN CONTENT -->
        <main class="main-content p-4 sm:p-6 lg:p-8">

            @if(session('success'))
                <div id="toastSuccess"
                    class="fixed top-5 right-5 z-50 flex items-center gap-3 bg-white border border-green-200 shadow-lg rounded-xl px-5 py-4 min-w-[280px]">
                    <div class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <p class="text-sm text-green-800 font-medium flex-1">{{ session('success') }}</p>
                    <button onclick="document.getElementById('toastSuccess').remove()"
                        class="text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <script>setTimeout(() => { const t = document.getElementById('toastSuccess'); if (t) t.remove(); }, 4000);</script>
            @endif

            <!-- Page Header -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">User Details</h2>
                    <p class="text-sm text-gray-500 mt-0.5">360° view of {{ $user->name }}'s account</p>
                </div>
                <a href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                    Back to Users
                </a>
            </div>

            @php
                $role = $user->roles->first()?->name ?? 'Customer';
                $roleColors = [
                    'System Admin' => 'bg-purple-100 text-purple-700',
                    'Customer' => 'bg-blue-100 text-blue-700',
                    'Driver' => 'bg-green-100 text-green-700',
                    'Restaurant Admin' => 'bg-yellow-100 text-yellow-700',
                    'Customer Support' => 'bg-pink-100 text-pink-700',
                    'Financial Accountant' => 'bg-orange-100 text-orange-700',
                    'Operations Manager' => 'bg-teal-100 text-teal-700',
                ];
                $roleBadge = $roleColors[$role] ?? 'bg-gray-100 text-gray-700';
            @endphp

            <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">

                <!-- ────────────────────────────────────────── -->
                <!-- LEFT: Profile Card                         -->
                <!-- ────────────────────────────────────────── -->
                <div class="xl:col-span-1 space-y-5">

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <!-- Cover -->
                        <div class="h-24 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500"></div>
                        <div class="px-5 pb-5 -mt-12">
                            <div class="relative inline-block">
                                <img class="w-20 h-20 rounded-xl border-4 border-white shadow-md object-cover"
                                    src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=4f46e5&color=fff&size=200"
                                    alt="{{ $user->name }}">
                                <span
                                    class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full border-2 border-white {{ strtolower($user->status) === 'active' ? 'bg-green-400' : 'bg-red-400' }}"></span>
                            </div>
                            <h3 class="mt-3 text-lg font-bold text-gray-900">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $roleBadge }}">{{ $role }}</span>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ strtolower($user->status) === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $user->status ?? 'Active' }}</span>
                            </div>
                            <div class="mt-4 space-y-2 text-sm text-gray-600">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ $user->phone ?? 'No phone' }}
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Joined {{ $user->created_at->format('d M Y') }}
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $accountAge }}
                                </div>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-2">
                                <a href="{{ route('admin.users.index') }}"
                                    class="text-center px-3 py-1.5 rounded-lg bg-indigo-50 text-primary text-xs font-semibold hover:bg-indigo-100 transition-colors">
                                    Edit Profile
                                </a>
                                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                                    onsubmit="return confirm('Delete this user permanently?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-full px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs font-semibold hover:bg-red-100 transition-colors">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Account Summary -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 divide-y divide-gray-100">
                        <div class="px-5 py-3">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Account Summary</p>
                        </div>
                        @if($user->hasRole('Customer'))
                            <div class="flex justify-between items-center px-5 py-3"><span
                                    class="text-sm text-gray-600">Total Orders</span><span
                                    class="text-sm font-bold text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full">{{ $totalOrders }}</span>
                            </div>
                            <div class="flex justify-between items-center px-5 py-3"><span
                                    class="text-sm text-gray-600">Total Spent</span><span
                                    class="text-sm font-bold text-green-600">${{ number_format($totalSpent, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center px-5 py-3"><span
                                    class="text-sm text-gray-600">Pending Orders</span><span
                                    class="text-sm font-bold text-yellow-600">{{ $pendingOrders }}</span></div>
                        @else
                            <div class="flex justify-between items-center px-5 py-3"><span
                                    class="text-sm text-gray-600">Role</span><span
                                    class="text-sm font-medium text-gray-800">{{ $role }}</span></div>
                            <div class="flex justify-between items-center px-5 py-3"><span
                                    class="text-sm text-gray-600">Permissions</span><span
                                    class="text-sm font-bold text-indigo-600">{{ $user->getAllPermissions()->count() }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center px-5 py-3"><span
                                class="text-sm text-gray-600">Member Since</span><span
                                class="text-sm text-gray-700">{{ $user->created_at->diffForHumans() }}</span></div>
                        <div class="flex justify-between items-center px-5 py-3"><span
                                class="text-sm text-gray-600">Last Updated</span><span
                                class="text-sm text-gray-700">{{ $user->updated_at->diffForHumans() }}</span></div>
                    </div>
                </div>

                <!-- ────────────────────────────────────────── -->
                <!-- RIGHT: Stats + Tabs                        -->
                <!-- ────────────────────────────────────────── -->
                <div class="xl:col-span-3 space-y-5">

                    <!-- Stat Cards -->
                    @if($user->hasRole('Customer'))
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                            @php
                                $statCards = [
                                    ['label' => 'Total Orders', 'value' => $totalOrders, 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', 'color' => 'text-indigo-600', 'bg' => 'bg-indigo-50'],
                                    ['label' => 'Total Spent', 'value' => '$' . number_format($totalSpent, 2), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'text-green-600', 'bg' => 'bg-green-50'],
                                    ['label' => 'Pending', 'value' => $pendingOrders, 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'text-yellow-600', 'bg' => 'bg-yellow-50'],
                                    ['label' => 'Days Active', 'value' => $user->created_at->diffInDays() . 'd', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'text-purple-600', 'bg' => 'bg-purple-50'],
                                ];
                            @endphp
                            @foreach($statCards as $sc)
                                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                                    <div class="flex items-center justify-between mb-3">
                                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                            {{ $sc['label'] }}</p>
                                        <div class="w-9 h-9 rounded-xl {{ $sc['bg'] }} flex items-center justify-center">
                                            <svg class="w-5 h-5 {{ $sc['color'] }}" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="{{ $sc['icon'] }}" />
                                            </svg>
                                        </div>
                                    </div>
                                    <p class="text-2xl font-bold text-gray-900">{{ $sc['value'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div
                            class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-5 text-white flex items-center gap-4 shadow-md">
                            <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-lg">Staff Member — {{ $role }}</p>
                                <p class="text-white/80 text-sm mt-0.5">This account has
                                    {{ $user->getAllPermissions()->count() }} permission(s) assigned.</p>
                            </div>
                        </div>
                    @endif

                    <!-- Tabs -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <!-- Tab Buttons -->
                        <div class="border-b border-gray-100 px-6">
                            <div class="flex -mb-px" id="tabButtons">
                                <button
                                    class="tab-btn active px-4 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors"
                                    data-tab="profile">Profile Info</button>
                                @if($user->hasRole('Customer'))
                                    <button
                                        class="tab-btn px-4 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors"
                                        data-tab="orders">Order History</button>
                                @else
                                    <button
                                        class="tab-btn px-4 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors"
                                        data-tab="activity">Activity Log</button>
                                @endif
                                <button
                                    class="tab-btn px-4 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors"
                                    data-tab="security">Security</button>
                            </div>
                        </div>

                        <!-- Tab 1: Profile Info -->
                        <div id="tab-profile" class="tab-panel active p-6">
                            <h4 class="text-base font-semibold text-gray-900 mb-4">Personal Information</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @php
                                    $fields = [
                                        ['label' => 'Full Name', 'value' => $user->name],
                                        ['label' => 'Email Address', 'value' => $user->email],
                                        ['label' => 'Phone Number', 'value' => $user->phone ?? '—'],
                                        ['label' => 'Role', 'value' => $role],
                                        ['label' => 'Status', 'value' => $user->status ?? 'Active'],
                                        ['label' => 'Registered On', 'value' => $user->created_at->format('d M Y, H:i')],
                                        ['label' => 'Last Updated', 'value' => $user->updated_at->format('d M Y, H:i')],
                                        ['label' => 'User ID', 'value' => '#' . $user->id],
                                    ];
                                @endphp
                                @foreach($fields as $f)
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">
                                            {{ $f['label'] }}</p>
                                        <p class="text-sm font-medium text-gray-800">{{ $f['value'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                            @if(!$user->hasRole('Customer') && $user->getAllPermissions()->count() > 0)
                                <div class="mt-6">
                                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Assigned Permissions</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($user->getAllPermissions() as $perm)
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">{{ $perm->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Tab 2a: Orders (Customer) -->
                        @if($user->hasRole('Customer'))
                            <div id="tab-orders" class="tab-panel p-6">
                                <h4 class="text-base font-semibold text-gray-900 mb-4">Order History</h4>
                                <div class="flex flex-col items-center justify-center py-16 text-center">
                                    <svg class="w-14 h-14 text-gray-200 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    <p class="text-sm font-medium text-gray-400">No orders yet</p>
                                    <p class="text-xs text-gray-300 mt-1">Will display once the Orders module is connected.
                                    </p>
                                </div>
                            </div>
                        @else
                            <!-- Tab 2b: Activity (Staff) -->
                            <div id="tab-activity" class="tab-panel p-6">
                                <h4 class="text-base font-semibold text-gray-900 mb-4">Activity Log</h4>
                                <div class="space-y-3">
                                    <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 16l-4-4m0 0l4-4m-4 4h14" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">Account created</p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                {{ $user->created_at->format('d M Y, H:i') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-yellow-50 text-yellow-600 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">Profile last updated</p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                {{ $user->updated_at->format('d M Y, H:i') }}</p>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-400 text-center pt-2">Full audit log will be available when
                                        the Activity module is connected.</p>
                                </div>
                            </div>
                        @endif

                        <!-- Tab 3: Security -->
                        <div id="tab-security" class="tab-panel p-6">
                            <h4 class="text-base font-semibold text-gray-900 mb-4">Security & Access</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-2">Email
                                        Verified</p>
                                    @if($user->email_verified_at)
                                        <span
                                            class="inline-flex items-center gap-1 text-sm font-medium text-green-700 bg-green-50 px-2.5 py-1 rounded-lg">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            Verified {{ $user->email_verified_at->format('d M Y') }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 text-sm font-medium text-red-600 bg-red-50 px-2.5 py-1 rounded-lg">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Not Verified
                                        </span>
                                    @endif
                                </div>
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-2">Account
                                        Status</p>
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium {{ strtolower($user->status) === 'active' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                        {{ $user->status ?? 'Active' }}
                                    </span>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-4 sm:col-span-2">
                                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-3">Quick
                                        Actions</p>
                                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}"
                                        class="inline">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="name" value="{{ $user->name }}">
                                        <input type="hidden" name="email" value="{{ $user->email }}">
                                        <input type="hidden" name="role" value="{{ $role }}">
                                        <input type="hidden" name="status"
                                            value="{{ strtolower($user->status) === 'active' ? 'Blocked' : 'Active' }}">
                                        <button type="submit"
                                            class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ strtolower($user->status) === 'active' ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                                            {{ strtolower($user->status) === 'active' ? '🔒 Block Account' : '✅ Activate Account' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div><!-- end tabs card -->
                </div><!-- end right -->
            </div><!-- end grid -->
        </main>
    </div><!-- end main area -->

    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    <script>
        // ── Tabs ──────────────────────────────────────────────────────
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById('tab-' + btn.dataset.tab)?.classList.add('active');
            });
        });

        // ── Sidebar ───────────────────────────────────────────────────
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
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

        // ── Profile Dropdown ──────────────────────────────────────────
        const profileDropdownBtn = document.getElementById('profileDropdownBtn');
        const profileDropdownMenu = document.getElementById('profileDropdownMenu');
        if (profileDropdownBtn) {
            profileDropdownBtn.addEventListener('click', e => {
                e.stopPropagation();
                profileDropdownMenu.classList.toggle('hidden');
            });
        }
        document.addEventListener('click', () => profileDropdownMenu?.classList.add('hidden'));
    </script>
</body>

</html>