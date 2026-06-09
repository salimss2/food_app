<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Drivers Management') }} - {{ __('Admin Dashboard') }}</title>
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
    @csrf

    @include('admin::layouts.partials.sidebar')










    <!-- Layout Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300">

        @include('admin::layouts.partials.header')

        <!-- Main Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto w-full">

            <!-- Page Header -->
            <div
                class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center sm:space-x-reverse sm:space-x-4 space-y-4 sm:space-y-0">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ __('Drivers Management') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ __('Manage delivery drivers and their vehicles.') }}</p>
                </div>
                <button onclick="openModal('driverModal')"
                    class="bg-primary hover:bg-primary_dark text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary flex items-center space-x-reverse space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>{{ __('Add Driver') }}</span>
                </button>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                <!-- Toolbar (Search & Counters) -->
                <div
                    class="p-4 border-b border-gray-200 flex flex-col xl:flex-row justify-between items-center space-y-3 xl:space-y-0">
                    <div class="relative w-full xl:max-w-xs">
                        <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" id="driverSearch" placeholder="{{ __('Search drivers by name, phone...') }}"
                            class="w-full h-9 ps-9 pe-3 rounded-md border border-gray-300 bg-white text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm">
                    </div>

                    <div class="flex flex-wrap items-center justify-center gap-2 text-xs font-medium">
                        {{-- Total --}}
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-full bg-gray-100 text-gray-600 shadow-sm border border-gray-200">
                            {{ __('Total') }} <span class="font-bold text-gray-900 ms-1">{{ $totalDrivers }}</span>
                        </span>

                        {{-- Online / Offline --}}
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-full bg-green-50 text-green-700 shadow-sm border border-green-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                            {{ __('Online') }} <span class="font-bold ms-1">{{ $onlineCount }}</span>
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-full bg-red-50 text-red-700 shadow-sm border border-red-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            {{ __('Offline') }} <span class="font-bold ms-1">{{ $offlineCount }}</span>
                        </span>

                        {{-- Status Counts --}}
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-full bg-blue-50 text-blue-700 shadow-sm border border-blue-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            {{ __('Active') }} <span class="font-bold ms-1">{{ $activeCount }}</span>
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-full bg-yellow-50 text-yellow-700 shadow-sm border border-yellow-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                            {{ __('Inactive') }} <span class="font-bold ms-1">{{ $inactiveCount }}</span>
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-full bg-gray-900 text-white shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            {{ __('Blocked') }} <span class="font-bold ms-1 text-gray-300">{{ $blockedCount }}</span>
                        </span>
                    </div>
                </div>

                <!-- Table Wrapper -->
                <div class="overflow-x-auto w-full">
                    <table class="w-full whitespace-nowrap text-start text-sm text-gray-500">
                        <thead
                            class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-3">{{ __('Driver Info') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Contact') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('ID #') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Vehicle') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('Status') }}</th>
                                <th scope="col" class="px-6 py-3 text-center">{{ __('Availability') }}</th>
                                <th scope="col" class="px-6 py-3 text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="driversTableBody" class="divide-y divide-gray-200 bg-white">
                            @forelse($drivers as $driver)
                                @php
                                    $profile = $driver->driverProfile;
                                    $availability = $driver->availability;
                                    $status = strtolower($driver->status ?? 'inactive');

                                    // Account Status Mapping
                                    if ($status === 'active') {
                                        $statusBadge = 'bg-blue-100 text-blue-800';
                                    } elseif ($status === 'inactive') {
                                        $statusBadge = 'bg-yellow-100 text-yellow-800';
                                    } else {
                                        $statusBadge = 'bg-gray-800 text-white';
                                    }
                                    $statusLabel = ucfirst($driver->status ?? 'Inactive');

                                    $isBlocked = $status !== 'active';
                                    $isOnline = $availability ? $availability->is_online : false;
                                @endphp
                                <tr class="hover:bg-gray-50 hover:shadow-sm transition-all duration-150">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <img class="h-8 w-8 rounded-full border border-gray-200 me-3 object-cover"
                                                src="{{ $profile->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($driver->name) . '&background=random' }}"
                                                alt="">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $driver->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $driver->phone ?? '—' }}</div>
                                        @if($driver->email)
                                            <div class="text-xs text-gray-500">{{ $driver->email }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $profile->id_number ?? '—' }}</td>
                                    <td class="px-6 py4">
                                        <div class="text-sm text-gray-900">{{ $profile->vehicle_model ?? '—' }}</div>
                                        <div class="text-xs text-gray-500 font-mono">{{ $profile->vehicle_plate ?? '' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusBadge }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center">
                                        @if($isBlocked)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-500 font-medium text-xs">
                                                <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                                {{ __('Blocked') }}
                                            </span>
                                        @else
                                            <button onclick="toggleAvailability({{ $driver->id }}, this)"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border focus:outline-none transition-all duration-200 {{ $isOnline ? 'border-green-200 bg-green-50 text-green-800 hover:bg-green-100' : 'border-red-200 bg-red-50 text-red-800 hover:bg-red-100' }}">
                                                <span
                                                    class="availability-dot w-2 h-2 rounded-full {{ $isOnline ? 'bg-green-500 animate-pulse' : 'bg-red-500' }}"></span>
                                                <span
                                                    class="availability-text font-medium">{{ $isOnline ? __('Online') : __('Offline') }}</span>
                                            </button>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-end text-sm font-medium space-x-reverse space-x-2">
                                        {{-- View Details Page --}}
                                        <a href="{{ route('admin.drivers.show', $driver->id) }}"
                                            class="inline-flex items-center px-3 py-1 bg-primary text-white text-xs font-semibold rounded shadow-sm hover:bg-primary_dark transition-colors me-1"
                                            title="{{ __('View Full Driver Profile') }}">
                                            <svg class="w-3.5 h-3.5 me-1.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ __('View Details') }}
                                        </a>
                                        {{-- Quick View Modal --}}
                                        @php
                                            $quickData = base64_encode(json_encode([
                                                'name' => $driver->name,
                                                'phone' => $driver->phone,
                                                'email' => $driver->email,
                                                'idNumber' => $profile->id_number ?? null,
                                                'address' => $profile->address ?? null,
                                                'vModel' => $profile->vehicle_model ?? null,
                                                'pNumber' => $profile->vehicle_plate ?? null,
                                                'vin' => $profile->vehicle_vin ?? null,
                                                'status' => $statusLabel,
                                                'avatar' => $profile->avatar_url ?? null,
                                            ]));
                                            $editData = base64_encode(json_encode([
                                                'id' => $driver->id,
                                                'name' => $driver->name,
                                                'email' => $driver->email,
                                                'phone' => $driver->phone,
                                                'status' => $statusLabel,
                                                'idNumber' => $profile->id_number ?? '',
                                                'address' => $profile->address ?? '',
                                                'vehicleModel' => $profile->vehicle_model ?? '',
                                                'vehiclePlate' => $profile->vehicle_plate ?? '',
                                                'vehicleVin' => $profile->vehicle_vin ?? '',
                                            ]));
                                        @endphp
                                        <button type="button" data-qv="{{ $quickData }}"
                                            onclick="openDetailsB64(this.dataset.qv)"
                                            class="text-indigo-600 hover:text-indigo-900 align-middle" title="Quick View">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </button>

                                        {{-- Delete --}}
                                        <button type="button" onclick="openDeleteModal({{ $driver->id }})"
                                            class="text-red-600 hover:text-red-900 align-middle" title="Delete">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr id="emptyStateRow">
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                            </svg>
                                            <p class="text-base font-semibold text-gray-500 mb-1">No Drivers Yet</p>
                                            <p class="text-sm text-gray-400 mb-4">Get started by adding your first delivery
                                                driver.</p>
                                            <button onclick="openModal('driverModal')"
                                                class="inline-flex items-center gap-2 bg-primary hover:bg-primary_dark text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                Add your first driver
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>


                <!-- Pagination -->
                <div class="border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                    {!! $drivers->links() !!}
                </div>
            </div>
        </main>
    </div>

    <!-- Add/Edit Driver Modal -->
    <div id="driverModal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-xl bg-white text-start shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl modal-content">
                    <form id="driverForm" method="POST" action="{{ route('admin.drivers.store') }}"
                        data-store-url="{{ route('admin.drivers.store') }}">
                        @csrf
                        <div id="driverMethodField"></div>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-gray-100">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4" id="driver-modal-title">
                                {{ __('Add Driver') }}
                            </h3>
                            <input type="hidden" id="driverId">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Personal Info Area -->
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 border-b pb-1 mb-3">
                                        {{ __('Personal Information') }}
                                    </h4>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">{{ __('Full Name') }}
                                                <span class="text-red-500">*</span></label>
                                            <input type="text" id="driverName" name="name"
                                                class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-sm"
                                                required>
                                            <span id="err-name" class="text-red-500 text-xs hidden mt-1 block"></span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">{{ __('Phone') }}
                                                <span class="text-red-500">*</span></label>
                                            <input type="tel" id="driverPhone" name="phone"
                                                class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-sm"
                                                required>
                                            <span id="err-phone" class="text-red-500 text-xs hidden mt-1 block"></span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">{{ __('Email') }}
                                                <span class="text-red-500">*</span></label>
                                            <input type="email" id="driverEmail" name="email" autocomplete="off"
                                                class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-sm"
                                                required>
                                            <span id="err-email" class="text-red-500 text-xs hidden mt-1 block"></span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">{{ __('ID Number') }}
                                                <span class="text-red-500">*</span></label>
                                            <input type="text" id="driverIdNumber" name="id_number"
                                                class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-sm"
                                                required>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700">{{ __('Password') }}</label>
                                            <input type="password" id="driverPassword" name="password"
                                                autocomplete="new-password"
                                                placeholder="{{ __('Leave empty to keep current') }}"
                                                class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-sm">
                                            <span id="err-password"
                                                class="text-red-500 text-xs hidden mt-1 block"></span>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                                            <select id="driverStatus" name="status"
                                                class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-sm">
                                                <option value="Active">{{ __('Active') }}</option>
                                                <option value="Blocked">{{ __('Blocked') }}</option>
                                                <option value="Inactive">{{ __('Inactive') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Vehicle Info Area -->
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 border-b pb-1 mb-3">
                                        {{ __('Vehicle Information') }}
                                    </h4>
                                    <div class="space-y-3 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700">{{ __('Vehicle Model') }}
                                                <span class="text-red-500">*</span></label>
                                            <input type="text" id="vehicleModel" name="vehicle_model"
                                                placeholder="e.g. Toyota Camry 2020"
                                                class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-sm"
                                                required>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700">{{ __('Plate Number') }}
                                                <span class="text-red-500">*</span></label>
                                            <input type="text" id="vehiclePlate" name="vehicle_plate"
                                                placeholder="e.g. ABC-1234"
                                                class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-sm"
                                                required>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700">{{ __('VIN (Vehicle Identification)') }}
                                                <span class="text-red-500">*</span></label>
                                            <input type="text" id="vehicleVin" name="vehicle_vin"
                                                class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-sm"
                                                required>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('Driver Address') }}</label>
                                        <textarea id="driverAddress" name="address" rows="2"
                                            class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-sm"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Status / Settings Area -->
                            <div class="mt-6 border-t border-gray-100 pt-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">{{ __('Settings & Status') }}</h4>
                                <div
                                    class="bg-gray-50 p-4 rounded-lg border border-gray-200 flex items-center justify-between">
                                    <div>
                                        <h5 class="text-sm font-bold text-gray-900">{{ __('Go Online Immediately') }}
                                            (تفعيل التوفر
                                            فوراً)</h5>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            {{ __('Toggle on to make the driver instantly available for assignments upon saving.') }}
                                        </p>
                                    </div>
                                    <label class="inline-flex relative items-center cursor-pointer">
                                        <input type="checkbox" name="is_online" value="1" class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500 shadow-sm">
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit"
                                class="inline-flex w-full justify-center rounded-md bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary_dark sm:ms-3 sm:w-auto">{{ __('Save Driver') }}</button>
                            <button type="button"
                                class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                                onclick="closeModal('driverModal')">{{ __('Cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Driver Details Quick-View Modal -->
    <div id="detailsModal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-xl bg-white text-start shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl modal-content">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                            <h3 class="text-xl font-bold text-gray-900">{{ __('Driver Profile') }}</h3>
                            <button onclick="closeModal('detailsModal')"
                                class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div
                            class="flex flex-col sm:flex-row items-center sm:items-start sm:space-x-reverse sm:space-x-6 mb-6">
                            <img id="detailAvatar"
                                class="w-24 h-24 rounded-full shadow-md object-cover border-2 border-primary" src=""
                                alt="">
                            <div class="mt-4 sm:mt-0 text-center sm:text-start">
                                <h4 class="text-2xl font-bold text-gray-900" id="detailName"></h4>
                                <span id="detailStatus"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold mt-1"></span>
                                <p class="text-sm text-gray-500 mt-2 flex items-center justify-center sm:justify-start">
                                    <svg class="w-4 h-4 me-1 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                        </path>
                                    </svg>
                                    <span id="detailPhone"></span>
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Background info card -->
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
                                <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                                    {{ __('Background Info') }}
                                </h5>
                                <div class="space-y-2 text-sm">
                                    <p><span class="font-medium text-gray-700">{{ __('Email') }}:</span> <span
                                            class="text-gray-600" id="detailEmail"></span></p>
                                    <p><span class="font-medium text-gray-700">{{ __('ID Number') }}:</span> <span
                                            class="text-gray-600" id="detailIdNumber"></span></p>
                                    <p><span class="font-medium text-gray-700">{{ __('Address') }}:</span> <span
                                            class="text-gray-600" id="detailAddress"></span></p>
                                </div>
                            </div>
                            <!-- Vehicle card highlighted -->
                            <div
                                class="bg-indigo-50 p-4 rounded-lg border border-indigo-100 shadow-sm relative overflow-hidden">
                                <div class="absolute -right-4 -top-4 opacity-10">
                                    <svg class="h-24 w-24 text-indigo-800" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                        <path
                                            d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7h-3v7h.05a2.5 2.5 0 004.9 0H17a1 1 0 001-1V9l-2-2h-2z" />
                                    </svg>
                                </div>
                                <h5
                                    class="text-xs font-bold text-indigo-800 uppercase tracking-wider mb-2 relative z-10">
                                    {{ __('Vehicle Details') }}
                                </h5>
                                <div class="space-y-2 text-sm relative z-10">
                                    <p><span class="font-medium text-indigo-900">{{ __('Model') }}:</span> <span
                                            class="text-indigo-700 font-semibold" id="detailVehicleModel"></span></p>
                                    <p><span class="font-medium text-indigo-900">{{ __('Plate') }}:</span> <span
                                            class="inline-block bg-yellow-300 text-yellow-900 px-2 py-0.5 rounded font-mono font-bold text-xs shadow-sm border border-yellow-400"
                                            id="detailPlate"></span></p>
                                    <p><span class="font-medium text-indigo-900">{{ __('VIN') }}:</span> <span
                                            class="text-indigo-700 text-xs font-mono" id="detailVin"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Driver Modal -->
    <div id="deleteModal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-xl bg-white text-start shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md modal-content">
                    <form id="deleteDriverForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
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
                                    <h3 class="text-lg font-semibold leading-6 text-gray-900">{{ __('Delete Driver') }}
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            {{ __('Are you sure you want to remove this driver? This action cannot be undone.') }}
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

    {{-- Flash Messages --}}
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

    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    {{-- Route URL templates must be defined BEFORE drivers.js loads --}}
    <script>
        const DRIVER_UPDATE_URL_TEMPLATE = '{{ url('admin/drivers') }}';
        const DRIVER_DELETE_URL_TEMPLATE = '{{ url('admin/drivers') }}';
        const DRIVER_TOGGLE_AVAILABILITY_URL = '{{ url('admin/drivers') }}';
    </script>
    <script src="{{ asset('modules/admin/js/drivers.js') }}"></script>
</body>

</html>