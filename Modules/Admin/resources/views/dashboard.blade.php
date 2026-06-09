@extends('admin::layouts.app')

@section('content')
    <!-- Page Title -->
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Dashboard Overview') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Welcome back, Admin. Here\'s what\'s happening today.') }}</p>
        </div>
    </div>

    <!-- Stats grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Stat Card 1 -->
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition-shadow">
            <div class="p-3 rounded-full bg-blue-50 text-blue-600 me-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">{{ __('Total Users') }}</p>
                <p class="text-2xl font-bold text-gray-900">12,408</p>
            </div>
        </div>
        <!-- Stat Card 2 -->
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition-shadow">
            <div class="p-3 rounded-full bg-indigo-50 text-indigo-600 me-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">{{ __('Active Drivers') }}</p>
                <p class="text-2xl font-bold text-gray-900">342</p>
            </div>
        </div>
        <!-- Stat Card 3 -->
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition-shadow">
            <div class="p-3 rounded-full bg-green-50 text-green-600 me-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">{{ __('Restaurants') }}</p>
                <p class="text-2xl font-bold text-gray-900">89</p>
            </div>
        </div>
        <!-- Stat Card 4 -->
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition-shadow">
            <div class="p-3 rounded-full bg-yellow-50 text-yellow-600 me-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">{{ __('Pending Payments') }}</p>
                <p class="text-2xl font-bold text-gray-900">$24,930</p>
            </div>
        </div>
    </div>

    <!-- Recent Activity Skeleton (Placeholder for Dashboard) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-5">
        <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Quick Links') }}</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.users.index') }}"
                class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-indigo-300 transition-colors">
                <div class="text-sm font-medium text-gray-900">{{ __('Manage Users') }} &rarr;</div>
                <div class="mt-1 text-xs text-gray-500">{{ __('View, edit, or block users.') }}</div>
            </a>
            <a href="{{ route('admin.drivers.index') }}"
                class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-indigo-300 transition-colors">
                <div class="text-sm font-medium text-gray-900">{{ __('Manage Drivers') }} &rarr;</div>
                <div class="mt-1 text-xs text-gray-500">{{ __('Onboard new delivery drivers.') }}</div>
            </a>
            <a href="{{ route('admin.restaurants.index') }}"
                class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-indigo-300 transition-colors">
                <div class="text-sm font-medium text-gray-900">{{ __('Manage Restaurants') }} &rarr;</div>
                <div class="mt-1 text-xs text-gray-500">{{ __('Add food menus and restaurants.') }}</div>
            </a>
            <a href="{{ route('admin.payments.index') }}"
                class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-indigo-300 transition-colors">
                <div class="text-sm font-medium text-gray-900">{{ __('Approve Payments') }} &rarr;</div>
                <div class="mt-1 text-xs text-gray-500">{{ __('Process wire deposits manually.') }}</div>
            </a>
        </div>
    </div>
@endsection