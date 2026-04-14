<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurants Management - Admin Dashboard</title>
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
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased overflow-hidden flex h-screen">

    <!-- Sidebar -->
    <!-- Mobile sidebar backdrop -->
    <div id="sidebarBackdrop" class="fixed inset-0 z-20 bg-gray-900 bg-opacity-50 lg:hidden hidden-el"></div>
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-gray-200 transition-transform duration-300 transform -translate-x-full lg:translate-x-0 lg:static lg:inset-0 lg:flex lg:flex-col shadow-sm">
        <div class="flex items-center justify-center h-16 border-b border-gray-200 px-6">
            <h1 class="text-xl font-bold text-gray-900 flex items-center space-x-2">
                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <span>AdminPanel</span>
            </h1>
        </div>
        
        <div class="overflow-y-auto overflow-x-hidden flex-grow shadow-inner">
            <ul class="flex flex-col py-4 space-y-1 px-3 mb-10">
                <!-- Dashboard -->
                <li class="px-2">
                    <div class="text-xs uppercase font-semibold text-gray-400 tracking-wider mb-2">Menu</div>
                </li>
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent px-6 group transition-colors">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span class="ml-3 font-medium text-sm">Dashboard</span>
                    </a>
                </li>
                
                <!-- Orders -->
                <li class="px-2 mt-4">
                    <div class="text-xs uppercase font-semibold text-gray-400 tracking-wider mb-2">Orders</div>
                </li>
                <li>
                    <a href="{{ route('admin.orders.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent px-6 group transition-colors">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <span class="ml-3 font-medium text-sm">Active Orders</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.scheduled-orders.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent px-6 group transition-colors">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="ml-3 font-medium text-sm">Scheduled Orders</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.order-history.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent px-6 group transition-colors">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="ml-3 font-medium text-sm">Order History</span>
                    </a>
                </li>

                <!-- Users & Profiles -->
                <li class="px-2 mt-4">
                    <div class="text-xs uppercase font-semibold text-gray-400 tracking-wider mb-2">Users & Roles</div>
                </li>
                <li>
                    <a href="{{ route('admin.users.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent px-6 group transition-colors">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span class="ml-3 font-medium text-sm">Users Collection</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.roles-permissions.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent px-6 group transition-colors">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <span class="ml-3 font-medium text-sm">Roles & Permissions</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.profile') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent px-6 group transition-colors">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span class="ml-3 font-medium text-sm">Admin Profile</span>
                    </a>
                </li>

                <!-- Restaurants & Drivers -->
                <li class="px-2 mt-4">
                    <div class="text-xs uppercase font-semibold text-gray-400 tracking-wider mb-2">Vendors & Fleet</div>
                </li>
                <li>
                    <a href="{{ route('admin.restaurants.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-primary bg-indigo-50 border-l-4 border-primary px-6 rounded-r-lg group transition-colors">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span class="ml-3 font-medium text-sm">Restaurants</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.drivers.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent px-6 group transition-colors">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        <span class="ml-3 font-medium text-sm">Drivers</span>
                    </a>
                </li>

                <!-- Financials -->
                <li class="px-2 mt-4">
                    <div class="text-xs uppercase font-semibold text-gray-400 tracking-wider mb-2">Financials</div>
                </li>
                <li>
                    <a href="{{ route('admin.payments.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent px-6 group transition-colors">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="ml-3 font-medium text-sm">Payments</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.revenue.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent px-6 group transition-colors">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        <span class="ml-3 font-medium text-sm">Revenue Management</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.withdrawals.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent px-6 group transition-colors">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        <span class="ml-3 font-medium text-sm">Withdrawals</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.commissions.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent px-6 group transition-colors">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path></svg>
                        <span class="ml-3 font-medium text-sm">Commissions</span>
                    </a>
                </li>

                <!-- Marketing & Support -->
                <li class="px-2 mt-4">
                    <div class="text-xs uppercase font-semibold text-gray-400 tracking-wider mb-2">Marketing & Support</div>
                </li>
                <li>
                    <a href="{{ route('admin.offers.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent px-6 group transition-colors">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path></svg>
                        <span class="ml-3 font-medium text-sm">Offers</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.discounts.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent px-6 group transition-colors">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        <span class="ml-3 font-medium text-sm">Discounts</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.complaints.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent px-6 group transition-colors">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span class="ml-3 font-medium text-sm">Complaints</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.feedback.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent px-6 group transition-colors">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                        <span class="ml-3 font-medium text-sm">Feedback</span>
                    </a>
                </li>

                <!-- Notifications -->
                <li class="px-2 mt-4">
                    <div class="text-xs uppercase font-semibold text-gray-400 tracking-wider mb-2">Notifications</div>
                </li>
                <li>
                    <a href="{{ route('admin.notifications.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent px-6 group transition-colors">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span class="ml-3 font-medium text-sm">Send Notification</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.scheduled-notifications.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent px-6 group transition-colors">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="ml-3 font-medium text-sm">Scheduled</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.notification-history.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent px-6 group transition-colors">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="ml-3 font-medium text-sm">History</span>
                    </a>
                </li>

                <!-- System -->
                <li class="px-2 mt-4">
                    <div class="text-xs uppercase font-semibold text-gray-400 tracking-wider mb-2">System</div>
                </li>
                <li>
                    <a href="{{ route('admin.reports.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent px-6 group transition-colors">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="ml-3 font-medium text-sm">Reports</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.settings.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent px-6 group transition-colors">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="ml-3 font-medium text-sm">Settings</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>










    <!-- Layout Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300">
        
        <!-- Navbar -->
        <header class="bg-white shadow-sm ring-1 ring-gray-200 z-10 w-full">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                <!-- Hamburger Menu -->
                <div class="flex items-center flex-1">
                    <button id="mobileMenuBtn" class="text-gray-500 focus:outline-none lg:hidden pl-1 pr-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <!-- Search -->
                    <div class="hidden md:flex w-full max-w-md ml-4">
                        <div class="relative w-full text-gray-400 focus-within:text-gray-600">
                            <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none pl-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" placeholder="Global search..." class="w-full h-10 pl-10 pr-3 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white focus:border-transparent transition-all sm:text-sm">
                        </div>
                    </div>
                </div>

                <!-- Right Nav Items -->
                <div class="flex items-center space-x-4">
                    <button class="relative p-2 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                        <span class="absolute top-1.5 right-1.5 block w-2 h-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </button>
                    <!-- Profile User Dropdown -->
                    <div class="relative">
                        <button id="profileDropdownBtn" class="flex items-center space-x-2 focus:outline-none">
                            <img class="w-8 h-8 rounded-full border-2 border-primary object-cover" src="https://ui-avatars.com/api/?name=Admin+User&background=4f46e5&color=fff">
                            <span class="hidden md:block font-medium text-sm text-gray-700">Admin</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="profileDropdownMenu" class="hidden-el absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 z-20">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Your Profile</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                            <div class="border-t border-gray-100"></div>
                            <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Sign out</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto w-full relative">
            
            <!-- SECTION: Restaurants List -->
            <div id="restaurantsListSection">
                <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Restaurants Management</h2>
                        <p class="text-sm text-gray-500 mt-1">Manage partners, view details, and control menus.</p>
                    </div>
                    <button onclick="openModal('restaurantModal')" class="bg-primary hover:bg-primary_dark text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        <span>Add Restaurant</span>
                    </button>
                </div>

                <!-- Toolbar (Search & Counters) -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                    <div class="p-4 flex flex-col xl:flex-row justify-between items-center space-y-3 xl:space-y-0">
                        <div class="relative w-full xl:max-w-xs">
                            <div class="absolute inset-y-1/2 left-3 flex items-center pointer-events-none -translate-y-1/2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <form action="{{ route('admin.restaurants.index') }}" method="GET">
                                <input type="text" name="search" id="restaurantSearch" value="{{ request('search') }}" placeholder="Search restaurants..." class="w-full h-9 pl-10 pr-3 rounded-md border border-gray-300 bg-white text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm">
                            </form>
                        </div>
                        
                        <div class="flex flex-wrap items-center justify-center gap-2 text-xs font-medium">
                            {{-- Total --}}
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-full bg-gray-100 text-gray-600 shadow-sm border border-gray-200">
                                Total <span class="font-bold text-gray-900 ml-1">{{ $totalRestaurants ?? 0 }}</span>
                            </span>
                            
                            {{-- Status Counts --}}
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-full bg-green-50 text-green-700 shadow-sm border border-green-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                Open <span class="font-bold ml-1">{{ $activeRestaurants ?? 0 }}</span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-full bg-red-50 text-red-700 shadow-sm border border-red-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                Closed <span class="font-bold ml-1">{{ $inactiveRestaurants ?? 0 }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto w-full">
                        <table class="w-full whitespace-nowrap text-left text-sm text-gray-500">
                            <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Restaurant</th>
                                    <th scope="col" class="px-6 py-3">Owner Contact</th>
                                    <th scope="col" class="px-6 py-3">Category</th>
                                    <th scope="col" class="px-6 py-3 text-center">Status</th>
                                    <th scope="col" class="px-6 py-3 text-center">State</th>
                                    <th scope="col" class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="restaurantsTableBody" class="divide-y divide-gray-200 bg-white">
                                @forelse($restaurants as $restaurant)
                                <tr id="restaurant-row-{{ $restaurant->id }}" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <img class="res-logo h-10 w-10 rounded-lg border border-gray-200 mr-3 object-cover" 
                                                 src="{{ $restaurant->logo_url }}" alt="">
                                            <div>
                                                <div class="res-name-text text-sm font-medium text-gray-900">{{ $restaurant->name }}</div>
                                                <div class="res-status-subtext text-xs text-gray-500">{{ $restaurant->status === 'open' ? 'Open' : 'Closed' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="res-owner-name text-sm text-gray-900">{{ $restaurant->owner?->name ?? 'No Manager' }}</div>
                                        <div class="res-owner-phone text-xs text-gray-400">{{ $restaurant->owner?->phone ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="res-category-badge px-2 py-1 bg-indigo-50 text-indigo-700 rounded text-xs font-semibold">{{ $restaurant->category }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $statusClass = match($restaurant->account_status) {
                                                'Active' => 'bg-green-100 text-green-800',
                                                'Blocked' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span class="res-account-status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                            {{ $restaurant->account_status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center flex justify-center">
                                        @php $isOpen = ($restaurant->status === 'open'); @endphp
                                        <button onclick="toggleState({{ $restaurant->id }}, this)" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border focus:outline-none transition-all duration-200 {{ $isOpen ? 'border-green-200 bg-green-50 text-green-800 hover:bg-green-100' : 'border-red-200 bg-red-50 text-red-800 hover:bg-red-100' }}">
                                            <span class="availability-dot w-2 h-2 rounded-full {{ $isOpen ? 'bg-green-500 animate-pulse' : 'bg-red-500' }}"></span>
                                            <span class="availability-text font-medium text-xs">{{ $isOpen ? 'Open' : 'Closed' }}</span>
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                                        @php
                                            $b64 = base64_encode($restaurant->loadMissing('owner')->toJson());
                                        @endphp
                                        <button onclick="openDetailsB64('{{ $b64 }}')" class="text-indigo-600 hover:text-indigo-900" title="Quick View">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        <button onclick="openEditB64('{{ $b64 }}')" class="text-blue-600 hover:text-blue-900" title="Edit"><svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                                        <button onclick="blockRestaurant({{ $restaurant->id }}, this)" class="text-red-500 hover:text-red-700" title="{{ $restaurant->account_status === 'Blocked' ? 'Unblock' : 'Block' }}">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        </button>
                                        <button onclick="openDeleteModal({{ $restaurant->id }})" class="text-red-600 hover:text-red-900" title="Delete"><svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <p class="text-lg font-medium">No restaurants found.</p>
                                        <p class="text-sm">Start by adding a new restaurant partner.</p>
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
                <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                    <div class="flex items-center space-x-4">
                        <button onclick="goBackToRestaurants()" class="text-gray-500 hover:text-gray-800 focus:outline-none border border-gray-300 rounded px-2 py-1 shadow-sm bg-white">
                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Back
                        </button>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900" id="menuTitle">Manage Menu</h2>
                            <p class="text-sm text-gray-500 mt-1">Add or edit meals for this restaurant.</p>
                        </div>
                    </div>
                    <button onclick="openModal('mealModal')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm focus:outline-none flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        <span>Add Meal</span>
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
    <div id="restaurantModal" class="relative z-50 hidden-el" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl modal-content">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4" id="restaurant-modal-title">Add Restaurant</h3>
                        <form id="restaurantForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="restaurantId" name="id">
                            <div id="methodContainer"></div>
                            
                            <div class="space-y-4">
                                <!-- Logo Upload -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Restaurant Logo</label>
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-grow">
                                            <input type="file" name="logo" id="rLogo" accept="image/*" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary_dark cursor-pointer">
                                        </div>
                                    </div>
                                </div>
                                <!-- Restaurant Info -->
                                <div class="bg-gray-50 p-3 rounded border border-gray-200">
                                    <h4 class="text-sm font-medium text-gray-700 border-b pb-1 mb-2">Restaurant Info</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Restaurant Name</label>
                                            <input type="text" id="rName" name="name" class="mt-1 block w-full rounded border-gray-300 py-1.5 px-2 text-sm shadow-sm focus:ring-primary focus:border-primary" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Category</label>
                                            <select id="rCategory" name="category" class="mt-1 block w-full rounded border-gray-300 py-1.5 px-2 text-sm shadow-sm focus:ring-primary focus:border-primary">
                                                <option value="Fast Food">Fast Food</option>
                                                <option value="Fine Dining">Fine Dining</option>
                                                <option value="Cafe">Cafe</option>
                                                <option value="Desserts">Desserts</option>
                                                <option value="Healthy">Healthy</option>
                                            </select>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-xs font-medium text-gray-700">Address / Location</label>
                                            <input type="text" id="rAddress" name="location" class="mt-1 block w-full rounded border-gray-300 py-1.5 px-2 text-sm shadow-sm focus:ring-primary focus:border-primary" required>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-xs font-medium text-gray-700">Status</label>
                                            <select id="rStatus" name="status" class="mt-1 block w-full rounded border-gray-300 py-1.5 px-2 text-sm shadow-sm focus:ring-primary focus:border-primary">
                                                <option value="open">Open (Active)</option>
                                                <option value="closed">Closed (Inactive)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- Owner Info -->
                                <div class="bg-gray-50 p-3 rounded border border-gray-200">
                                    <h4 class="text-sm font-medium text-gray-700 border-b pb-1 mb-2">Owner Contact Info</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Owner Name</label>
                                            <input type="text" id="rOwner" name="owner_name" class="mt-1 block w-full rounded border-gray-300 py-1.5 px-2 text-sm shadow-sm focus:ring-primary focus:border-primary" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Phone</label>
                                            <input type="tel" id="rPhone" name="owner_phone" class="mt-1 block w-full rounded border-gray-300 py-1.5 px-2 text-sm shadow-sm focus:ring-primary focus:border-primary" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Email (Username)</label>
                                            <input type="email" id="rEmail" name="owner_email" class="mt-1 block w-full rounded border-gray-300 py-1.5 px-2 text-sm shadow-sm focus:ring-primary focus:border-primary" required>
                                        </div>
                                        <div id="rPasswordField">
                                            <label class="block text-xs font-medium text-gray-700">Password</label>
                                            <input type="password" id="rPassword" name="password" autocomplete="new-password" placeholder="Min 8 characters" class="mt-1 block w-full rounded border-gray-300 py-1.5 px-2 text-sm shadow-sm focus:ring-primary focus:border-primary">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-xs font-medium text-gray-700">Role</label>
                                            <select id="rRole" class="mt-1 block w-full rounded border-gray-300 py-1.5 px-2 text-sm shadow-sm focus:ring-primary focus:border-primary">
                                                <option value="Restaurant Admin">Restaurant Admin</option>
                                                <option value="Admin">Admin</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" class="inline-flex w-full justify-center rounded-md bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary_dark sm:ml-3 sm:w-auto" onclick="saveRestaurant()">Save Restaurant</button>
                        <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" onclick="closeModal('restaurantModal')">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Restaurant Details Modal -->
    <div id="restaurantDetailsModal" class="relative z-50 hidden-el" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-content">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 text-center">
                        <!-- Logo in Details -->
                        <div class="mb-4">
                            <img id="detailResLogo" src="" class="h-24 w-24 rounded-xl border-2 border-gray-100 mx-auto object-cover shadow-sm" alt="Logo">
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2" id="detailResName"></h3>
                        <p class="text-sm font-medium text-indigo-600 mb-6" id="detailResCategory"></p>
                        
                        <div class="grid grid-cols-2 gap-4 text-left border-t border-gray-100 pt-4">
                            <div>
                                <span class="block text-xs uppercase text-gray-400 font-bold mb-1">Owner Name</span>
                                <span class="text-sm text-gray-800" id="detailResOwner"></span>
                            </div>
                            <div>
                                <span class="block text-xs uppercase text-gray-400 font-bold mb-1">Contact Phone</span>
                                <span class="text-sm text-gray-800" id="detailResPhone"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="block text-xs uppercase text-gray-400 font-bold mb-1">Email</span>
                                <span class="text-sm text-gray-800" id="detailResEmail"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="block text-xs uppercase text-gray-400 font-bold mb-1">Full Address</span>
                                <span class="text-sm text-gray-800" id="detailResAddress"></span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" onclick="closeModal('restaurantDetailsModal')">Close</button>
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
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md modal-content">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4" id="meal-modal-title">Add Meal</h3>
                        <form id="mealForm">
                            <input type="hidden" id="mealId">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Meal Image</label>
                                    <div class="mt-1 flex justify-center rounded-md border-2 border-dashed border-gray-300 px-6 py-5">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                            <div class="flex text-sm text-gray-600 justify-center">
                                                <label class="relative cursor-pointer rounded-md bg-white font-medium text-primary hover:text-primary_dark">
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
                                    <input type="text" id="mealName" class="mt-1 block w-full rounded border-gray-300 py-2 px-3 text-sm shadow-sm focus:ring-primary focus:border-primary" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Price ($)</label>
                                    <input type="number" step="0.01" id="mealPrice" class="mt-1 block w-full rounded border-gray-300 py-2 px-3 text-sm shadow-sm focus:ring-primary focus:border-primary" required>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 sm:ml-3 sm:w-auto" onclick="saveMeal()">Save Meal</button>
                        <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" onclick="closeModal('mealModal')">Cancel</button>
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
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md modal-content">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-lg font-semibold leading-6 text-gray-900">Are you sure?</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">This action cannot be undone.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto" onclick="confirmDelete()">Delete</button>
                        <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" onclick="closeModal('deleteModal')">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    <script src="{{ asset('modules/admin/js/restaurants.js') }}"></script>

</body>
</html>
