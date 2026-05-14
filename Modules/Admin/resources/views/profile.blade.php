<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - Dashboard</title>
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
        html[dir="rtl"] body { font-family: 'Cairo', sans-serif !important; }
        html[dir="rtl"] .ml-3 { margin-left: 0 !important; margin-right: 0.75rem !important; }
        html[dir="rtl"] .ml-4 { margin-left: 0 !important; margin-right: 1rem !important; }
        html[dir="rtl"] aside { left: auto !important; right: 0 !important; border-right: none !important; border-left: 1px solid #e5e7eb !important; }
        html[dir="rtl"] .space-x-4 > :not([hidden]) ~ :not([hidden]) { --tw-space-x-reverse: 1 !important; }
        html[dir="rtl"] .space-x-2 > :not([hidden]) ~ :not([hidden]) { --tw-space-x-reverse: 1 !important; }
        html[dir="rtl"] #langDropdownMenu, html[dir="rtl"] #profileDropdownMenu { right: auto !important; left: 0 !important; }
    </style></head>

<body
    class="bg-gradient-to-br from-gray-50 to-indigo-50/30 text-gray-800 font-sans antialiased flex h-screen overflow-hidden">

    @include('admin::layouts.partials.sidebar')

    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300">
        @include('admin::layouts.partials.header')

        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto w-full">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Admin Profile</h2>
                <p class="text-sm text-gray-500 mt-1">Manage your account settings and personal information.</p>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-md shadow-sm">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column: Profile Card -->
                    <div class="lg:col-span-1">
                        <div
                            class="bg-white rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-primary p-6 flex flex-col items-center text-center relative overflow-hidden">
                            <div
                                class="absolute top-0 left-0 w-full h-24 bg-gradient-to-r from-primary to-primary_dark opacity-10">
                            </div>

                            <div class="relative mb-4 mt-6 group">
                                <label for="avatar_upload" class="cursor-pointer block relative rounded-full">
                                    <img id="avatar_preview"
                                        class="w-28 h-28 rounded-full border-4 border-white shadow-md object-cover bg-white transition-opacity duration-300 group-hover:opacity-80"
                                        src="{{ Auth::user()->image_path ? asset(Auth::user()->image_path) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=4f46e5&color=fff&size=128' }}"
                                        alt="Avatar">
                                    <div
                                        class="absolute inset-0 flex items-center justify-center rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-black bg-opacity-30">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                </label>
                                <input type="file" id="avatar_upload" name="avatar" class="hidden" accept="image/*"
                                    onchange="previewImage(event)">
                                @error('avatar') <span class="text-xs text-red-500 mt-2 block">{{ $message }}</span>
                                @enderror
                                <span
                                    class="absolute bottom-2 right-2 w-4 h-4 bg-success border-2 border-white rounded-full shadow-sm z-10 pointer-events-none"></span>
                            </div>

                            <h3 class="text-xl font-bold text-gray-900">{{ Auth::user()->name }}</h3>
                            <p class="text-sm text-gray-500 mb-4">{{ Auth::user()->email }}</p>

                            <div
                                class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-primary text-xs font-bold mb-6 border border-indigo-100">
                                {{ Auth::check() && Auth::user()->roles->count() > 0 ? Auth::user()->roles->first()->name : 'System Admin' }}
                            </div>

                            <div class="w-full pt-5 border-t border-gray-100 text-left space-y-4">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500 font-medium flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                            </path>
                                        </svg>
                                        Phone
                                    </span>
                                    <span
                                        class="text-gray-900 font-medium">{{ Auth::user()->phone ?? 'Not Provided' }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500 font-medium flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        Joined
                                    </span>
                                    <span
                                        class="text-gray-900 font-medium">{{ Auth::user()->created_at ? Auth::user()->created_at->format('M Y') : 'Unknown' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Edit Form -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sm:p-8">
                            <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 mb-6">Edit
                                Information
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                                <!-- Name -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}"
                                        required
                                        class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:border-primary focus:ring-primary shadow-sm h-11 px-4 transition-colors @error('name') border-red-500 @enderror">
                                    @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Phone Number</label>
                                    <input type="text" name="phone" value="{{ old('phone', Auth::user()->phone) }}"
                                        class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:border-primary focus:ring-primary shadow-sm h-11 px-4 transition-colors @error('phone') border-red-500 @enderror">
                                    @error('phone') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address <span
                                            class="text-red-500">*</span></label>
                                    <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                                        required
                                        class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:border-primary focus:ring-primary shadow-sm h-11 px-4 transition-colors @error('email') border-red-500 @enderror">
                                    @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 mb-4 mt-8">Security
                                Configuration</h3>
                            <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 mb-6 flex items-start">
                                <svg class="w-5 h-5 text-blue-500 mr-2 flex-shrink-0 mt-0.5" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-xs sm:text-sm text-blue-800">
                                    Leave the password fields blank if you do not wish to change your current password.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                                <!-- Password -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">New Password</label>
                                    <input type="password" name="password" autocomplete="new-password"
                                        class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:border-primary focus:ring-primary shadow-sm h-11 px-4 transition-colors @error('password') border-red-500 @enderror"
                                        placeholder="••••••••">
                                    @error('password') <span
                                    class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Confirm New
                                        Password</label>
                                    <input type="password" name="password_confirmation" autocomplete="new-password"
                                        class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:border-primary focus:ring-primary shadow-sm h-11 px-4 transition-colors"
                                        placeholder="••••••••">
                                </div>
                            </div>

                            <div class="flex justify-end pt-5 border-t border-gray-100">
                                <button type="submit"
                                    class="inline-flex justify-center items-center px-6 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-primary hover:bg-primary_dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors w-full sm:w-auto">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                                        </path>
                                    </svg>
                                    Save Profile Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </main>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                const output = document.getElementById('avatar_preview');
                output.src = reader.result;
            };
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }
    </script>
</body>

</html>