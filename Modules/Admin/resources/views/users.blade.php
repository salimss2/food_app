<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - Admin Dashboard</title>
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
    </style>
</head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased overflow-hidden flex h-screen">

    @include('admin::layouts.partials.sidebar')










    <!-- Layout Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300">

        @include('admin::layouts.partials.header')

        <!-- Toast Flash Messages -->
        @if(session('success'))
            <div id="toastSuccess"
                class="fixed top-5 right-5 z-50 flex items-center gap-3 bg-white border border-green-200 shadow-lg rounded-xl px-5 py-4 min-w-[280px] animate-pulse"
                role="alert">
                <div class="flex-shrink-0 w-9 h-9 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-green-800">{{ __('Success') }}</p>
                    <p class="text-xs text-green-600 mt-0.5">{{ session('success') }}</p>
                </div>
                <button onclick="document.getElementById('toastSuccess').remove()"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <script>setTimeout(() => { const t = document.getElementById('toastSuccess'); if (t) t.remove(); }, 4000);</script>
        @endif

        @if(session('error'))
            <div id="toastError"
                class="fixed top-5 right-5 z-50 flex items-center gap-3 bg-white border border-red-200 shadow-lg rounded-xl px-5 py-4 min-w-[280px]"
                role="alert">
                <div class="flex-shrink-0 w-9 h-9 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-red-800">{{ __('Error') }}</p>
                    <p class="text-xs text-red-600 mt-0.5">{{ session('error') }}</p>
                </div>
                <button onclick="document.getElementById('toastError').remove()"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <script>setTimeout(() => { const t = document.getElementById('toastError'); if (t) t.remove(); }, 4000);</script>
        @endif

        <!-- Main Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto w-full">

            <!-- Page Header -->
            <div
                class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ __('Users Management') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ __('Manage platform administrators and customers.') }}</p>
                </div>
                <button onclick="openModal('userModal')"
                    class="bg-primary hover:bg-primary_dark text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>{{ __('Add User') }}</span>
                </button>
            </div>

            {{-- Inline Validation Errors (persist modal open on next render) --}}
            @if($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-red-800 mb-1">{{ __('Please fix the following errors:') }}
                            </p>
                            <ul class="text-xs text-red-700 space-y-0.5 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Table Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                <!-- Toolbar (Search & Filter) -->
                <div
                    class="p-4 border-b border-gray-200 flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0">

                    <!-- Tabs & Role Filter -->
                    <div
                        class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
                        <!-- Tailwind Tabs -->
                        <div class="flex space-x-1 bg-gray-100/80 p-1 rounded-lg border border-gray-200">
                            <a href="{{ request()->fullUrlWithQuery(['tab' => 'Active']) }}"
                                class="px-4 py-1.5 text-sm font-medium rounded-md transition-colors {{ request('tab', 'Active') === 'Active' ? 'bg-white shadow-sm text-primary' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/50' }}">{{ __('Active') }}</a>
                            <a href="{{ request()->fullUrlWithQuery(['tab' => 'Blocked']) }}"
                                class="px-4 py-1.5 text-sm font-medium rounded-md transition-colors {{ request('tab') === 'Blocked' ? 'bg-white shadow-sm text-red-600' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/50' }}">{{ __('Blocked') }}</a>
                            <a href="{{ request()->fullUrlWithQuery(['tab' => 'Archived']) }}"
                                class="px-4 py-1.5 text-sm font-medium rounded-md transition-colors {{ request('tab') === 'Archived' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/50' }}">{{ __('Archived') }}</a>
                        </div>

                        <!-- Role Filter -->
                        <form method="GET" action="{{ route('admin.users.index') }}" class="w-full sm:w-auto">
                            @if(request('tab')) <input type="hidden" name="tab" value="{{ request('tab') }}"> @endif
                            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            <select name="role_filter" onchange="this.form.submit()"
                                class="h-9 block w-full rounded-md border border-gray-300 bg-white py-1 ps-3 pe-8 text-sm text-gray-700 shadow-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                                <option value="all">{{ __('All Roles') }}</option>
                                @foreach($filterRoles as $role)
                                    <option value="{{ $role->name }}" {{ request('role_filter') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <!-- Backend Native Search -->
                    <div class="relative w-full sm:max-w-xs">
                        <form method="GET" action="{{ route('admin.users.index') }}">
                            @if(request('tab')) <input type="hidden" name="tab" value="{{ request('tab') }}"> @endif
                            @if(request('role_filter')) <input type="hidden" name="role_filter"
                            value="{{ request('role_filter') }}"> @endif
                            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="{{ __('Search users by name, email...') }}"
                                class="w-full h-9 ps-9 pe-3 rounded-md border border-gray-300 bg-white text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm">
                        </form>
                    </div>
                </div>

                <!-- Table Wrapper -->
                <div class="overflow-x-auto w-full">
                    <table class="w-full whitespace-nowrap text-start text-sm text-gray-500">
                        <thead
                            class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-3">{{ __('ID') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Name') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Email') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Phone') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Role') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Status') }}</th>
                                <th scope="col" class="px-6 py-3 text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody" class="divide-y divide-gray-200 bg-white">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-xs font-medium text-gray-900">#{{ $user->id }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <img class="h-8 w-8 rounded-full border border-gray-200 me-3"
                                                src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random"
                                                alt="">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $user->phone ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $user->roles->first()?->name ?? 'Customer' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ strtolower($user->status) === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $user->status }}
                                        </span>
                                    </td>
                                    <td class="actions-cell px-6 py-4 text-end text-sm font-medium space-x-2">
                                        @php
                                            $viewData = [
                                                'id' => $user->id,
                                                'name' => $user->name,
                                                'email' => $user->email,
                                                'phone' => $user->phone,
                                                'role' => $user->roles->first()?->name ?? 'Customer',
                                                'status' => $user->status,
                                                'created_at' => $user->created_at->format('d M Y, H:i'),
                                            ];
                                        @endphp
                                        <button type="button" onclick="openViewModal({{ json_encode($viewData) }})"
                                            class="text-indigo-600 hover:text-indigo-900" title="View User">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <button type="button"
                                            onclick="openEditModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}', '{{ $user->phone }}', '{{ $user->roles->first()?->name ?? 'Customer' }}', '{{ $user->status }}')"
                                            class="text-blue-600 hover:text-blue-900" title="Edit"><svg
                                                class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                </path>
                                            </svg></button>
                                        <button type="button" onclick="openDeleteModal({{ $user->id }})"
                                            class="text-red-600 hover:text-red-900" title="Delete"><svg
                                                class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg></button>

                                        <form method="POST" action="{{ route('admin.users.toggleBlock', $user->id) }}"
                                            class="inline-block align-middle ms-2">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="inline-flex items-center px-2.5 py-1.5 rounded text-xs font-medium border transition-colors {{ strtolower($user->status) === 'active' ? 'bg-red-50 text-red-600 border-red-200 hover:bg-red-100' : 'bg-green-50 text-green-600 border-green-200 hover:bg-green-100' }}">
                                                {{ strtolower($user->status) === 'active' ? __('حظر (Block)') : __('تفعيل (Activate)') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                        {{ __('No users found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination UI -->
                <div class="border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                    {!! $users->links() !!}
                </div>
            </div>
        </main>
    </div>

    <!-- Add/Edit User Modal -->
    <div id="userModal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-xl bg-white text-start shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md modal-content">
                    <form id="userForm" method="POST" action="{{ route('admin.users.store') }}"
                        data-store-url="{{ route('admin.users.store') }}">
                        @csrf
                        <div id="methodContainer"></div>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-gray-100">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:ms-4 sm:mt-0 sm:text-start w-full">
                                    <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                                        {{ __('Add User') }}
                                    </h3>

                                    @if($errors->any())
                                        <div class="mt-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm">
                                            <ul class="list-disc pl-5">
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <div class="mt-4 space-y-4">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700">{{ __('Full Name') }}</label>
                                            <input type="text" id="userName" name="name"
                                                class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-sm"
                                                required>
                                        </div>
                                        <div class="mt-3">
                                            <label
                                                class="block text-sm font-medium text-gray-700">{{ __('Email Address') }}</label>
                                            <input type="email" id="userEmail" name="email"
                                                class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-sm"
                                                required>
                                        </div>
                                        <div class="mt-3">
                                            <label
                                                class="block text-sm font-medium text-gray-700">{{ __('Phone') }}</label>
                                            <input type="tel" id="userPhone" name="phone"
                                                class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-sm">
                                        </div>
                                        <div class="mt-3">
                                            <label
                                                class="block text-sm font-medium text-gray-700">{{ __('Password') }}</label>
                                            <input type="password" id="userPassword" name="password"
                                                placeholder="{{ __('Auto-generated if empty') }}"
                                                class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-sm">
                                        </div>
                                        <div class="mt-3">
                                            <label
                                                class="block text-sm font-medium text-gray-700">{{ __('Role') }}</label>
                                            <select id="userRole" name="role"
                                                class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-sm">
                                                @foreach($roles as $role)
                                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mt-3">
                                            <label
                                                class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                                            <select id="userStatus" name="status"
                                                class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-sm">
                                                <option value="Active">{{ __('Active') }}</option>
                                                <option value="Blocked">{{ __('Blocked') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit"
                                class="inline-flex w-full justify-center rounded-md bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary_dark sm:ms-3 sm:w-auto">{{ __('Save User') }}</button>
                            <button type="button"
                                class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                                onclick="closeModal('userModal')">{{ __('Cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md modal-content">
                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:ms-4 sm:mt-0 sm:text-start">
                                    <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                                        {{ __('Delete User') }}
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            {{ __('Are you sure you want to delete this user? All of their data will be permanently removed. This action cannot be undone.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit"
                                class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ms-3 sm:w-auto">{{ __('Delete') }}</button>
                            <button type="button"
                                class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                                onclick="closeModal('deleteModal')">{{ __('Cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════ -->
    <!-- View User Modal                             -->
    <!-- ═══════════════════════════════════════════ -->
    <div id="viewUserModal" class="relative z-50 hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/60 transition-opacity" onclick="closeModal('viewUserModal')"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all w-full sm:max-w-lg">

                    {{-- Header --}}
                    <div
                        class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-white">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900">{{ __('User Profile') }}</h3>
                        </div>
                        <button onclick="closeModal('viewUserModal')"
                            class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Avatar + Name --}}
                    <div class="flex flex-col items-center py-6 px-6 border-b border-gray-100 bg-gray-50">
                        <img id="view_avatar" src="" alt=""
                            class="w-20 h-20 rounded-full border-4 border-white shadow-md object-cover">
                        <h4 id="view_name" class="mt-3 text-lg font-bold text-gray-900">—</h4>
                        <p id="view_email" class="text-sm text-gray-500 mt-0.5">—</p>
                        <div class="mt-2 flex items-center gap-2">
                            <span id="view_role_badge"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">—</span>
                            <span id="view_status_badge"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">—</span>
                        </div>
                    </div>

                    {{-- Details grid --}}
                    <div class="divide-y divide-gray-50 px-6">
                        <div class="grid grid-cols-3 py-3">
                            <span
                                class="text-xs font-medium text-gray-500 uppercase tracking-wide col-span-1">{{ __('User ID') }}</span>
                            <span id="view_id" class="text-sm text-gray-800 col-span-2 font-mono">#—</span>
                        </div>
                        <div class="grid grid-cols-3 py-3">
                            <span
                                class="text-xs font-medium text-gray-500 uppercase tracking-wide col-span-1">{{ __('Phone') }}</span>
                            <span id="view_phone" class="text-sm text-gray-800 col-span-2">—</span>
                        </div>
                        <div class="grid grid-cols-3 py-3">
                            <span
                                class="text-xs font-medium text-gray-500 uppercase tracking-wide col-span-1">{{ __('Status') }}</span>
                            <span id="view_status" class="text-sm text-gray-800 col-span-2">—</span>
                        </div>
                        <div class="grid grid-cols-3 py-3">
                            <span
                                class="text-xs font-medium text-gray-500 uppercase tracking-wide col-span-1">{{ __('Role') }}</span>
                            <span id="view_role" class="text-sm text-gray-800 col-span-2">—</span>
                        </div>
                        <div class="grid grid-cols-3 py-3">
                            <span
                                class="text-xs font-medium text-gray-500 uppercase tracking-wide col-span-1">{{ __('Joined') }}</span>
                            <span id="view_created_at" class="text-sm text-gray-800 col-span-2">—</span>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 bg-gray-50 flex items-center justify-between border-t border-gray-100">
                        <a id="view_profile_link" href="#"
                            class="text-sm text-primary hover:underline font-medium">{{ __('View Full Profile') }} →</a>
                        <button onclick="closeModal('viewUserModal')"
                            class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">{{ __('Close') }}</button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    <script src="{{ asset('modules/admin/js/users.js') }}"></script>

    {{-- Auto-open modal if validation errors exist (form was just submitted) --}}
    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Check if we have old input from an edit (has _method PUT) or add
                const modal = document.getElementById('userModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    // If old input has an id, it's an edit action
                    @if(old('_method') === 'PUT')
                        document.getElementById('modal-title').innerText = 'Edit User';
                        document.getElementById('methodContainer').innerHTML = '<input type="hidden" name="_method" value="PUT">';
                    @else
                        document.getElementById('modal-title').innerText = 'Add User';
                    @endif
                    // Repopulate from old() input
                    @if(old('name')) document.getElementById('userName').value = @json(old('name')); @endif
                    @if(old('email')) document.getElementById('userEmail').value = @json(old('email')); @endif
                    @if(old('phone')) document.getElementById('userPhone').value = @json(old('phone')); @endif
                    @if(old('role')) document.getElementById('userRole').value = @json(old('role')); @endif
                    @if(old('status')) document.getElementById('userStatus').value = @json(old('status')); @endif
                            }
            });
        </script>
    @endif
</body>

</html>