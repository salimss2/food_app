<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Restaurants Management') }} - {{ __('Admin Dashboard') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto w-full relative">

            <!-- SECTION: Restaurants List -->
            <div id="restaurantsListSection">
                <div
                    class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ __('Restaurants Management') }}</h2>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ __('Manage partners, view details, and control menus.') }}</p>
                    </div>
                    <button onclick="openModal('restaurantModal')"
                        class="bg-primary hover:bg-primary_dark text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary flex items-center space-x-reverse space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>{{ __('Add Restaurant') }}</span>
                    </button>
                </div>

                <!-- Toolbar (Search & Counters) -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                    <div class="p-4 flex flex-col xl:flex-row justify-between items-center space-y-3 xl:space-y-0">
                        <div class="relative w-full xl:max-w-xs">
                            <div
                                class="absolute inset-y-1/2 start-3 flex items-center pointer-events-none -translate-y-1/2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <form action="{{ route('admin.restaurants.index') }}" method="GET">
                                <input type="text" name="search" id="restaurantSearch" value="{{ request('search') }}"
                                    placeholder="{{ __('Search restaurants...') }}"
                                    class="w-full h-9 ps-10 pe-3 rounded-md border border-gray-300 bg-white text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm">
                            </form>
                        </div>

                        <div class="flex flex-wrap items-center justify-center gap-2 text-xs font-medium"
                            id="filterToolbar">
                            {{-- All --}}
                            <button onclick="filterRestaurants('all', this)"
                                class="filter-btn active-filter inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-primary text-white shadow-sm transition-all">
                                {{ __('Total') }} <span class="font-bold ms-1">{{ $totalRestaurants ?? 0 }}</span>
                            </button>

                            {{-- State Filters --}}
                            <button onclick="filterRestaurants('open', this)"
                                class="filter-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white text-gray-600 shadow-sm border border-gray-200 hover:bg-green-50 hover:text-green-700 transition-all">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                {{ __('Online') }} <span class="font-bold ms-1">{{ $activeRestaurants ?? 0 }}</span>
                            </button>
                            <button onclick="filterRestaurants('closed', this)"
                                class="filter-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white text-gray-600 shadow-sm border border-gray-200 hover:bg-red-50 hover:text-red-700 transition-all">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                {{ __('Offline') }} <span class="font-bold ms-1">{{ $inactiveRestaurants ?? 0 }}</span>
                            </button>

                            <div class="w-px h-4 bg-gray-300 mx-1"></div>

                            {{-- Account Status Filters --}}
                            <button onclick="filterRestaurants('active', this)"
                                class="filter-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white text-gray-600 shadow-sm border border-gray-200 hover:bg-indigo-50 hover:text-indigo-700 transition-all">
                                {{ __('Active') }}
                            </button>
                            <button onclick="filterRestaurants('inactive', this)"
                                class="filter-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white text-gray-600 shadow-sm border border-gray-200 hover:bg-yellow-50 hover:text-yellow-700 transition-all">
                                {{ __('Inactive') }}
                            </button>
                            <button onclick="filterRestaurants('blocked', this)"
                                class="filter-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white text-gray-600 shadow-sm border border-gray-200 hover:bg-red-50 hover:text-red-700 transition-all">
                                {{ __('Blocked') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto w-full">
                        <table class="w-full whitespace-nowrap text-start text-sm text-gray-500">
                            <thead
                                class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200">
                                <tr>
                                    <th scope="col" class="px-6 py-3">{{ __('Restaurant') }}</th>
                                    <th scope="col" class="px-6 py-3">{{ __('Owner Contact') }}</th>
                                    <th scope="col" class="px-6 py-3">{{ __('Category') }}</th>
                                    <th scope="col" class="px-6 py-3 text-center">{{ __('Status') }}</th>
                                    <th scope="col" class="px-6 py-3 text-center">{{ __('State') }}</th>
                                    <th scope="col" class="px-6 py-3 text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="restaurantsTableBody" class="divide-y divide-gray-200 bg-white">
                                @forelse($restaurants as $restaurant)
                                    <tr id="restaurant-row-{{ $restaurant->id }}"
                                        data-state="{{ strtolower($restaurant->status) }}"
                                        data-account-status="{{ strtolower($restaurant->account_status) }}"
                                        class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                @php
                                                    $rawLogo = $restaurant->getRawOriginal('logo');
                                                    $logoUrl = $rawLogo ? (\Illuminate\Support\Str::startsWith($rawLogo, ['http://', 'https://']) ? $rawLogo : asset('storage/' . ltrim($rawLogo, '/'))) : asset('assets/default-restaurant.png');
                                                @endphp
                                                <img src="{{ $logoUrl }}"
                                                    alt="{{ $restaurant->name }}"
                                                    onerror="this.onerror=null;this.src='{{ asset('assets/default-restaurant.png') }}';"
                                                    class="res-logo h-10 w-10 rounded-lg border border-gray-200 me-3 object-cover">
                                                <div>
                                                    <div class="res-name-text text-sm font-medium text-gray-900">
                                                        {{ $restaurant->name }}</div>
                                                    <div class="res-status-subtext text-xs text-gray-500">
                                                        {{ $restaurant->status === 'open' ? __('Open') : __('Closed') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="res-owner-name text-sm text-gray-900">
                                                {{ $restaurant->owner?->name ?? __('No Manager') }}</div>
                                            <div class="res-owner-phone text-xs text-gray-400">
                                                {{ $restaurant->owner?->phone ?? '' }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="res-category-badge px-2 py-1 bg-indigo-50 text-indigo-700 rounded text-xs font-semibold">{{ $restaurant->category }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $statusClass = match ($restaurant->account_status) {
                                                    'Active' => 'bg-green-100 text-green-800',
                                                    'Blocked' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800',
                                                };
                                            @endphp
                                            <span
                                                class="res-account-status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                                {{ __($restaurant->account_status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center flex justify-center">
                                            @php $isOpen = ($restaurant->status === 'open'); @endphp
                                            <button onclick="toggleState({{ $restaurant->id }}, this)"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border focus:outline-none transition-all duration-200 {{ $isOpen ? 'border-green-200 bg-green-50 text-green-800 hover:bg-green-100' : 'border-red-200 bg-red-50 text-red-800 hover:bg-red-100' }}">
                                                <span
                                                    class="availability-dot w-2 h-2 rounded-full {{ $isOpen ? 'bg-green-500 animate-pulse' : 'bg-red-500' }}"></span>
                                                <span
                                                    class="availability-text font-medium text-xs">{{ $isOpen ? __('Open') : __('Closed') }}</span>
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 text-end text-sm font-medium space-x-reverse space-x-2">
                                            <a href="{{ route('admin.restaurants.show', $restaurant->id) }}" class="text-indigo-600 hover:text-indigo-900 inline-block" title="{{ __('عرض التفاصيل') }}">
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                            </a>
                                            @php
                                                $b64 = base64_encode($restaurant->loadMissing('owner')->toJson());
                                            @endphp
                                            <button onclick="openDetailsB64('{{ $b64 }}')"
                                                class="text-indigo-600 hover:text-indigo-900"
                                                title="{{ __('Quick View') }}">
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <button onclick="openEditB64('{{ $b64 }}')"
                                                class="text-blue-600 hover:text-blue-900" title="{{ __('Edit') }}"><svg
                                                    class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                    </path>
                                                </svg></button>
                                            <button onclick="blockRestaurant({{ $restaurant->id }}, this)"
                                                class="text-red-500 hover:text-red-700"
                                                title="{{ $restaurant->account_status === 'Blocked' ? __('Unblock') : __('Block') }}">
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                </svg>
                                            </button>
                                            <button onclick="openDeleteModal({{ $restaurant->id }})"
                                                class="text-red-600 hover:text-red-900" title="{{ __('Delete') }}"><svg
                                                    class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                            <p class="text-lg font-medium">{{ __('No restaurants found.') }}</p>
                                            <p class="text-sm">{{ __('Start by adding a new restaurant partner.') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SECTION: Menu Management -->
            <div id="menuManagementSection" class="hidden-el">
                <div
                    class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                    <div class="flex items-center space-x-reverse space-x-4">
                        <button onclick="goBackToRestaurants()"
                            class="text-gray-500 hover:text-gray-800 focus:outline-none border border-gray-300 rounded px-2 py-1 shadow-sm bg-white">
                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg> {{ __('Back') }}
                        </button>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900" id="menuTitle">{{ __('Manage Menu') }}</h2>
                            <p class="text-sm text-gray-500 mt-1">{{ __('Add or edit meals for this restaurant.') }}</p>
                        </div>
                    </div>
                    <button onclick="openModal('mealModal')"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm focus:outline-none flex items-center space-x-reverse space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>{{ __('Add Meal') }}</span>
                    </button>
                </div>

                <!-- Meals Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" id="mealsGrid">
                    <!-- JS Populated -->
                </div>
            </div>

        </main>
    </div>

    <!-- Modals -->

    <!-- Add/Edit Restaurant Modal -->
    <div id="restaurantModal" class="relative z-50 hidden-el" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl modal-content">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4" id="restaurant-modal-title">
                            {{ __('Add Restaurant') }}</h3>
                        <form id="restaurantForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="restaurantId" name="id">
                            <div id="methodContainer"></div>

                            <div class="space-y-4">
                                <!-- Logo Upload -->
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-700 mb-1">{{ __('Restaurant Logo') }}</label>
                                    <div class="flex items-center space-x-reverse space-x-4">
                                        <div class="flex-grow">
                                            <input type="file" name="logo" id="rLogo" accept="image/*"
                                                class="block w-full text-xs text-gray-500 file:me-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary_dark cursor-pointer">
                                        </div>
                                    </div>
                                </div>
                                <!-- Restaurant Info -->
                                <div class="bg-gray-50 p-3 rounded border border-gray-200">
                                    <h4 class="text-sm font-medium text-gray-700 border-b pb-1 mb-2">
                                        {{ __('Restaurant Info') }}</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-700">{{ __('Restaurant Name') }}</label>
                                            <input type="text" id="rName" name="name"
                                                class="mt-1 block w-full rounded border-gray-300 py-1.5 px-2 text-sm shadow-sm focus:ring-primary focus:border-primary"
                                                required>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-700">{{ __('Category') }}</label>
                                            <select id="rCategory" name="category"
                                                class="mt-1 block w-full rounded border-gray-300 py-1.5 px-2 text-sm shadow-sm focus:ring-primary focus:border-primary">
                                                <option value="Fast Food">{{ __('Fast Food') }}</option>
                                                <option value="Fine Dining">{{ __('Fine Dining') }}</option>
                                                <option value="Cafe">{{ __('Cafe') }}</option>
                                                <option value="Desserts">{{ __('Desserts') }}</option>
                                                <option value="Healthy">{{ __('Healthy') }}</option>
                                            </select>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label
                                                class="block text-xs font-medium text-gray-700">{{ __('Address / Location') }}</label>
                                            <input type="text" id="rAddress" name="location"
                                                class="mt-1 block w-full rounded border-gray-300 py-1.5 px-2 text-sm shadow-sm focus:ring-primary focus:border-primary"
                                                required>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label
                                                class="block text-xs font-medium text-gray-700">{{ __('Status') }}</label>
                                            <select id="rStatus" name="status"
                                                class="mt-1 block w-full rounded border-gray-300 py-1.5 px-2 text-sm shadow-sm focus:ring-primary focus:border-primary">
                                                <option value="open">{{ __('Open (Active)') }}</option>
                                                <option value="closed">{{ __('Closed (Inactive)') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- Owner Info -->
                                <div class="bg-gray-50 p-3 rounded border border-gray-200">
                                    <h4 class="text-sm font-medium text-gray-700 border-b pb-1 mb-2">
                                        {{ __('Owner Contact Info') }}</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-700">{{ __('Owner Name') }}</label>
                                            <input type="text" id="rOwner" name="owner_name"
                                                class="mt-1 block w-full rounded border-gray-300 py-1.5 px-2 text-sm shadow-sm focus:ring-primary focus:border-primary"
                                                required>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-700">{{ __('Phone') }}</label>
                                            <input type="tel" id="rPhone" name="owner_phone"
                                                class="mt-1 block w-full rounded border-gray-300 py-1.5 px-2 text-sm shadow-sm focus:ring-primary focus:border-primary"
                                                required>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-700">{{ __('Email (Username)') }}</label>
                                            <input type="email" id="rEmail" name="owner_email"
                                                class="mt-1 block w-full rounded border-gray-300 py-1.5 px-2 text-sm shadow-sm focus:ring-primary focus:border-primary"
                                                required>
                                        </div>
                                        <div id="rPasswordField">
                                            <label
                                                class="block text-xs font-medium text-gray-700">{{ __('Password') }}</label>
                                            <input type="password" id="rPassword" name="password"
                                                autocomplete="new-password" placeholder="{{ __('Min 8 characters') }}"
                                                class="mt-1 block w-full rounded border-gray-300 py-1.5 px-2 text-sm shadow-sm focus:ring-primary focus:border-primary">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label
                                                class="block text-xs font-medium text-gray-700">{{ __('Role') }}</label>
                                            <select id="rRole"
                                                class="mt-1 block w-full rounded border-gray-300 py-1.5 px-2 text-sm shadow-sm focus:ring-primary focus:border-primary">
                                                <option value="Restaurant Admin">{{ __('Restaurant Admin') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button"
                            class="inline-flex w-full justify-center rounded-md bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary_dark sm:ms-3 sm:w-auto"
                            onclick="saveRestaurant()">{{ __('Save Restaurant') }}</button>
                        <button type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                            onclick="closeModal('restaurantModal')">{{ __('Cancel') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Restaurant Details Modal -->
    <div id="restaurantDetailsModal" class="relative z-50 hidden-el" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-content">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 text-center">
                        <!-- Logo in Details -->
                        <div class="mb-4">
                            <img id="detailResLogo" src=""
                                class="h-24 w-24 rounded-xl border-2 border-gray-100 mx-auto object-cover shadow-sm"
                                alt="Logo">
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2" id="detailResName"></h3>
                        <p class="text-sm font-medium text-indigo-600 mb-6" id="detailResCategory"></p>

                        <div class="grid grid-cols-2 gap-4 text-start border-t border-gray-100 pt-4">
                            <div>
                                <span
                                    class="block text-xs uppercase text-gray-400 font-bold mb-1">{{ __('Owner Name') }}</span>
                                <span class="text-sm text-gray-800" id="detailResOwner"></span>
                            </div>
                            <div>
                                <span
                                    class="block text-xs uppercase text-gray-400 font-bold mb-1">{{ __('Contact Phone') }}</span>
                                <span class="text-sm text-gray-800" id="detailResPhone"></span>
                            </div>
                            <div class="col-span-2">
                                <span
                                    class="block text-xs uppercase text-gray-400 font-bold mb-1">{{ __('Email') }}</span>
                                <span class="text-sm text-gray-800" id="detailResEmail"></span>
                            </div>
                            <div class="col-span-2">
                                <span
                                    class="block text-xs uppercase text-gray-400 font-bold mb-1">{{ __('Full Address') }}</span>
                                <span class="text-sm text-gray-800" id="detailResAddress"></span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                            onclick="closeModal('restaurantDetailsModal')">{{ __('Close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Meal Modal -->
    <div id="mealModal" class="relative z-50 hidden-el" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md modal-content">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4" id="meal-modal-title">
                            {{ __('Add Meal') }}</h3>
                        <form id="mealForm">
                            <input type="hidden" id="mealId">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Meal Image</label>
                                    <div
                                        class="mt-1 flex justify-center rounded-md border-2 border-dashed border-gray-300 px-6 py-5">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor"
                                                fill="none" viewBox="0 0 48 48">
                                                <path
                                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600 justify-center">
                                                <label
                                                    class="relative cursor-pointer rounded-md bg-white font-medium text-primary hover:text-primary_dark">
                                                    <span>Upload a file</span>
                                                    <input type="file" class="sr-only">
                                                </label>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Meal Name</label>
                                    <input type="text" id="mealName"
                                        class="mt-1 block w-full rounded border-gray-300 py-2 px-3 text-sm shadow-sm focus:ring-primary focus:border-primary"
                                        required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Price ($)</label>
                                    <input type="number" step="0.01" id="mealPrice"
                                        class="mt-1 block w-full rounded border-gray-300 py-2 px-3 text-sm shadow-sm focus:ring-primary focus:border-primary"
                                        required>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button"
                            class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 sm:ms-3 sm:w-auto"
                            onclick="saveMeal()">{{ __('Save Meal') }}</button>
                        <button type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                            onclick="closeModal('mealModal')">{{ __('Cancel') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Generic Delete Modal -->
    <div id="deleteModal" class="relative z-50 hidden-el" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md modal-content">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ms-4 sm:mt-0 sm:text-start">
                                <h3 class="text-lg font-semibold leading-6 text-gray-900">{{ __('Are you sure?') }}</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">{{ __('This action cannot be undone.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button"
                            class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ms-3 sm:w-auto"
                            onclick="confirmDelete()">{{ __('Delete') }}</button>
                        <button type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                            onclick="closeModal('deleteModal')">{{ __('Cancel') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    <script src="{{ asset('modules/admin/js/restaurants.js') }}"></script>

</body>

</html>