<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enterprise Settings - Admin Dashboard</title>
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
                        danger: '#ef4444'
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

        html[dir="rtl"] .mr-2 {
            margin-right: 0 !important;
            margin-left: 0.5rem !important;
        }

        html[dir="rtl"] .-ml-1 {
            margin-left: 0 !important;
            margin-right: -0.25rem !important;
        }

        html[dir="rtl"] aside {
            left: auto !important;
            right: 0 !important;
            border-right: none !important;
            border-left: 1px solid #e5e7eb !important;
        }

        html[dir="rtl"] .space-x-8> :not([hidden])~ :not([hidden]) {
            --tw-space-x-reverse: 1 !important;
        }

        html[dir="rtl"] .text-left {
            text-align: right !important;
        }

        html[dir="rtl"] .text-right {
            text-align: left !important;
        }

        .hidden-el {
            display: none !important;
        }

        .modal-overlay {
            animation: fadeIn 0.2s ease-out;
        }

        .modal-content {
            animation: slideUp 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased overflow-hidden flex h-screen">

    @include('admin::layouts.partials.sidebar')

    <!-- Layout Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300">

        @include('admin::layouts.partials.header')

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto w-full bg-gray-50">

            <!-- Sub Navigation Tabs -->
            <div class="bg-white border-b border-gray-200 px-4 sm:px-6 lg:px-8 pt-4">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="{{ route('admin.commissions.index') }}"
                        class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors">
                        {{ __('Overview') }}
                    </a>
                    <a href="{{ route('admin.commissions-restaurant.index') }}"
                        class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors">
                        {{ __('Restaurant Commissions') }}
                    </a>
                    <a href="{{ route('admin.commissions-driver.index') }}"
                        class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors">
                        {{ __('Driver Commissions') }}
                    </a>
                    <a href="{{ route('admin.commissions-settings.index') }}"
                        class="border-primary text-primary whitespace-nowrap border-b-2 py-4 px-1 text-sm font-bold">
                        {{ __('Enterprise Settings') }} (الإعدادات المتقدمة)
                    </a>
                </nav>
            </div>

            <div class="p-4 sm:p-6 lg:p-8 pb-0">
                @if (session('success'))
                    <div class="mb-4 flex items-center p-4 text-sm text-green-800 border border-green-300 rounded-xl bg-green-50 shadow-sm"
                        role="alert">
                        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                        </svg>
                        <span class="sr-only">Success</span>
                        <div>
                            <span class="font-bold">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 flex items-center p-4 text-sm text-red-800 border border-red-300 rounded-xl bg-red-50 shadow-sm"
                        role="alert">
                        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                        </svg>
                        <span class="sr-only">Error</span>
                        <div>
                            <span class="font-bold">{{ __('Please correct the errors below.') }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <div class="p-4 sm:p-6 lg:p-8">

                <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ __('Distance Slabs Configuration') }} (إعدادات
                            شرائح المسافة)</h2>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ __('Manage dynamic delivery fees based on customer-to-restaurant distance.') }}
                        </p>
                    </div>
                    <button onclick="openModal()"
                        class="inline-flex items-center px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-xl shadow-lg hover:bg-primary_dark transition-all transform hover:scale-105 active:scale-95">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        {{ __('Add New Slab') }} (إضافة شريحة جديدة)
                    </button>
                </div>

                <!-- Settings Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-500">
                            <thead
                                class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200">
                                <tr>
                                    <th scope="col" class="px-6 py-4">{{ __('Min Distance (Km)') }} / من (كم)</th>
                                    <th scope="col" class="px-6 py-4">{{ __('Max Distance (Km)') }} / إلى (كم)</th>
                                    <th scope="col" class="px-6 py-4">{{ __('Total Delivery Fee ($)') }} / إجمالي الرسوم
                                    </th>
                                    <th scope="col" class="px-6 py-4">{{ __('Driver Share ($)') }} / حصة الموصل</th>
                                    <th scope="col" class="px-6 py-4">{{ __('Platform Share ($)') }} / حصة المنصة</th>
                                    <th scope="col" class="px-6 py-4 text-right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="slabsTableBody" class="divide-y divide-gray-200">
                                @forelse($slabs as $slab)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $slab->min_distance }} Km</td>
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $slab->max_distance }} Km</td>
                                        <td class="px-6 py-4 font-bold text-primary">
                                            ${{ number_format($slab->total_fee, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-green-600 font-semibold">
                                            ${{ number_format($slab->driver_share, 2) }}</td>
                                        <td class="px-6 py-4 text-indigo-600 font-semibold">
                                            ${{ number_format($slab->platform_share, 2) }}</td>
                                        <td class="px-6 py-4 text-right flex justify-end space-x-2">
                                            <button onclick="editSlab({{ $slab->toJson() }})"
                                                class="p-2 text-gray-400 hover:text-primary transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                    </path>
                                                </svg>
                                            </button>
                                            <form action="{{ route('admin.commissions-settings.destroy', $slab->id) }}"
                                                method="POST" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-gray-400 hover:text-danger transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                            {{ __('No distance slabs configured yet.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer Action -->
                <div class="mt-8 flex justify-end">
                    <button onclick="saveChanges()"
                        class="px-8 py-3.5 bg-success text-white font-bold rounded-xl shadow-xl hover:bg-green-600 transition-all transform hover:scale-105 active:scale-95 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        {{ __('Save Configuration') }} (حفظ الإعدادات)
                    </button>
                </div>

            </div>
        </main>
    </div>

    <!-- Modal for Adding/Editing Slabs -->
    <div id="slabModal" class="relative z-50 hidden-el" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-content">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900" id="modalTitle">{{ __('Add Distance Slab') }}</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500 transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form id="slabForm" action="{{ route('admin.commissions-settings.store') }}" method="POST"
                        class="px-6 py-6 space-y-4">
                        @csrf
                        <div id="methodField"></div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-1">{{ __('Min Distance (Km)') }}</label>
                                <input type="number" step="0.1" name="min_distance" id="min_distance" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                                    placeholder="0.0">
                                @error('min_distance')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-1">{{ __('Max Distance (Km)') }}</label>
                                <input type="number" step="0.1" name="max_distance" id="max_distance" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                                    placeholder="5.0">
                                @error('max_distance')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-1">{{ __('Total Delivery Fee ($)') }}</label>
                            <input type="number" step="0.01" name="total_fee" id="total_fee" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                                placeholder="0.00">
                            @error('total_fee')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-1">{{ __('Driver Share ($)') }}</label>
                                <input type="number" step="0.01" name="driver_share" id="driver_share" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                                    placeholder="0.00">
                                @error('driver_share')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-1">{{ __('Platform Share ($)') }}</label>
                                <input type="number" step="0.01" name="platform_share" id="platform_share" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                                    placeholder="0.00">
                                @error('platform_share')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="pt-4 flex space-x-3">
                            <button type="button" onclick="closeModal()"
                                class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-lg hover:bg-gray-200 transition-colors">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit" id="submitBtn"
                                class="flex-1 px-4 py-2 bg-primary text-white font-bold rounded-lg hover:bg-primary_dark transition-colors">
                                {{ __('Save Slab') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    <script>
        function openModal() {
            document.getElementById('slabModal').classList.remove('hidden-el');
        }

        function closeModal() {
            document.getElementById('slabModal').classList.add('hidden-el');
        }

        function handleSlabSubmit(event) {
            event.preventDefault();
            // Simplified logic for demo
            alert('New slab added successfully (Mock)');
            closeModal();
        }

        function saveChanges() {
            alert('Settings saved successfully (Mock)');
        }
    </script>
</body>

</html>