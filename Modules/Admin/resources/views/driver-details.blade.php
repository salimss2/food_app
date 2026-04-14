<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Driver Details - Admin Dashboard</title>
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
    <div id="sidebarBackdrop" class="fixed inset-0 z-20 bg-gray-900 bg-opacity-50 lg:hidden hidden"></div>
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
                    <a href="{{ route('admin.restaurants.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent px-6 group transition-colors">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span class="ml-3 font-medium text-sm">Restaurants</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.drivers.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-primary bg-indigo-50 border-l-4 border-primary px-6 rounded-r-lg group transition-colors">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
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
        <header class="bg-white shadow-sm ring-1 ring-gray-200 z-10">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                <!-- Hamburger Menu -->
                <div class="flex items-center flex-1">
                    <button id="mobileMenuBtn" class="text-gray-500 focus:outline-none lg:hidden pl-1 pr-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <!-- Back Button -->
                    <div class="hidden md:flex items-center">
                        <a href="{{ route('admin.drivers.index') }}" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Back to Drivers
                        </a>
                    </div>
                </div>

                <!-- Right Nav Items -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <button class="relative p-2 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                        <span class="absolute top-1.5 right-1.5 block w-2 h-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </button>
                    <!-- Profile User Dropdown -->
                    <div class="relative">
                        <button id="profileDropdownBtn" class="flex items-center space-x-2 focus:outline-none">
                            <img class="w-8 h-8 rounded-full border-2 border-primary object-cover" src="https://ui-avatars.com/api/?name=Admin+User&background=4f46e5&color=fff" alt="Admin avatar">
                            <span class="hidden md:block font-medium text-sm text-gray-700">Admin</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content (Driver Full Details) -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto w-full">
            
@php
    $profile     = $driver->driverProfile;
    $availability= $driver->availability;
    $status      = strtolower($driver->status ?? 'inactive');
    $isActive    = $status === 'active';
    $statusBadge = $isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
    $statusLabel = ucfirst($driver->status ?? 'Inactive');
    $dotColor    = $isActive ? 'bg-green-500' : 'bg-red-500';
    $avatarUrl   = $profile->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($driver->name) . '&background=4f46e5&color=fff&size=128';
    $isOnline    = $availability ? $availability->is_online : false;
@endphp
            <!-- Header Section (Top Card) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex flex-col sm:flex-row justify-between items-center sm:items-start gap-4">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                        <img class="w-24 h-24 rounded-full border-4 border-indigo-50 shadow-md object-cover" src="{{ $avatarUrl }}" alt="Driver image">
                        <div class="text-center sm:text-left mt-2">
                            <h2 class="text-2xl font-bold text-gray-900">{{ $driver->name }}</h2>
                            <div class="flex items-center justify-center sm:justify-start gap-3 mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusBadge }}">
                                    {{ $statusLabel }}
                                </span>
                                <div class="flex items-center text-yellow-400">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span class="ml-1 text-sm font-semibold text-gray-700">4.5</span>
                                </div>
                                <span class="text-sm text-gray-500">(120 Reviews)</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-2 flex items-center justify-center sm:justify-start">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                {{ $driver->phone ?? '—' }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <button onclick="openModal('notificationModal')" class="bg-primary hover:bg-primary_dark text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <span>Send Notification</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Left Sidebar Panel (Mini Card) -->
                <div class="w-full lg:w-1/4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sticky top-20">
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b pb-2">Profile Stats</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider">Account Status</p>
                                <div class="mt-1 flex items-center">
                                    <div class="w-2 h-2 rounded-full {{ $dotColor }} mr-2"></div>
                                    <span class="font-medium text-sm text-gray-900">{{ $statusLabel }}</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Live Availability</p>
                                @if(strtolower($driver->status ?? 'inactive') !== 'active')
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-500 font-medium text-xs">
                                        <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                        Blocked
                                    </div>
                                @else
                                    <button id="detailsAvailabilityToggle" onclick="toggleDetailsAvailability({{ $driver->id }}, this)" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border focus:outline-none transition-all duration-200 {{ $isOnline ? 'border-green-200 bg-green-50 text-green-800 hover:bg-green-100' : 'border-red-200 bg-red-50 text-red-800 hover:bg-red-100' }}">
                                        <span class="availability-dot w-2 h-2 rounded-full {{ $isOnline ? 'bg-green-500 animate-pulse' : 'bg-red-500' }}"></span>
                                        <span class="availability-text font-medium">{{ $isOnline ? 'Online' : 'Offline' }}</span>
                                    </button>
                                @endif
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider">Total Deliveries</p>
                                <p class="mt-1 font-semibold text-lg text-gray-900">1,245</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider">Join Date</p>
                                <p class="mt-1 font-medium text-sm text-gray-900">{{ $driver->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="pt-4 border-t border-gray-100">
                                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Quick Actions</h3>
                                <div class="space-y-2">
                                    <button class="w-full flex items-center justify-between px-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition" onclick="toggleStatus()">
                                        <span id="statusActionText">Block Driver</span>
                                        <div id="statusToggleBtn" class="w-8 h-4 rounded-full bg-green-400 relative transition-colors duration-300">
                                            <div class="absolute right-0.5 top-0.5 bg-white w-3 h-3 rounded-full shadow-sm transition-transform duration-300"></div>
                                        </div>
                                    </button>
                                    <button onclick="passwordReset()" class="w-full flex items-center px-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                        Reset Password
                                    </button>
                                    <button onclick="switchTab('documents')" class="w-full flex items-center px-3 py-2 border border-primary text-primary rounded-lg text-sm font-medium hover:bg-indigo-50 transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z"></path></svg>
                                        View ID Documents
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content (Tabs System) -->
                <div class="w-full lg:w-3/4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        
                        <!-- Tabs Header -->
                        <div class="flex overflow-x-auto border-b border-gray-200 hide-scrollbar">
                            <button onclick="switchTab('orders')" id="tab-orders" class="tab-btn active shrink-0 px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent transition-colors focus:outline-none">
                                Orders
                            </button>
                            <button onclick="switchTab('map')" id="tab-map" class="tab-btn shrink-0 px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent transition-colors focus:outline-none">
                                Map
                            </button>
                            <button onclick="switchTab('financial')" id="tab-financial" class="tab-btn shrink-0 px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent transition-colors focus:outline-none">
                                Financial
                            </button>
                            <button onclick="switchTab('reviews')" id="tab-reviews" class="tab-btn shrink-0 px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent transition-colors focus:outline-none">
                                Reviews
                            </button>
                            <button onclick="switchTab('documents')" id="tab-documents" class="tab-btn shrink-0 px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent transition-colors focus:outline-none">
                                Documents
                            </button>
                            <button onclick="switchTab('activity')" id="tab-activity" class="tab-btn shrink-0 px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent transition-colors focus:outline-none">
                                Activity
                            </button>
                        </div>

                        <!-- Tabs Content -->
                        <div class="p-6">
                            
                            <!-- Tab: Orders -->
                            <div id="content-orders" class="tab-content">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-bold text-gray-900">Recent Orders (Last 20)</h3>
                                    <div class="relative w-64">
                                        <input type="text" placeholder="Search orders..." class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                                        <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                </div>
                                <div class="overflow-x-auto w-full border border-gray-200 rounded-lg">
                                    <table class="w-full whitespace-nowrap text-left text-sm text-gray-500">
                                        <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200">
                                            <tr>
                                                <th scope="col" class="px-6 py-3">Order ID</th>
                                                <th scope="col" class="px-6 py-3">Date</th>
                                                <th scope="col" class="px-6 py-3">Restaurant</th>
                                                <th scope="col" class="px-6 py-3">Customer</th>
                                                <th scope="col" class="px-6 py-3">Amount</th>
                                                <th scope="col" class="px-6 py-3">Status</th>
                                                <th scope="col" class="px-6 py-3">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="ordersTableBody" class="divide-y divide-gray-200 bg-white">
                                            <!-- Populated via JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab: Map -->
                            <div id="content-map" class="tab-content hidden-el">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Driver Live Location</h3>
                                <!-- Map Placeholder -->
                                <div class="w-full h-96 bg-gray-200 rounded-xl relative overflow-hidden border border-gray-300 flex items-center justify-center">
                                    <!-- Use a placeholder background image to simulate a map -->
                                    <img src="https://images.unsplash.com/photo-1524661135-423995f22d0b?auto=format&fit=crop&w=1200&q=80" alt="Map view" class="absolute inset-0 w-full h-full object-cover opacity-80">
                                    
                                    <!-- Map Overlay elements -->
                                    <div class="absolute inset-x-0 top-0 bg-gradient-to-b from-gray-900/50 to-transparent p-4 text-white">
                                        <div class="flex items-center text-sm font-medium">
                                            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse mr-2"></span>
                                            Tracking Active - Last updated: Just now
                                        </div>
                                    </div>
                                    
                                    <!-- Driver Marker (Pulsing) -->
                                    <div class="absolute z-10 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 flex flex-col items-center">
                                        <div class="relative flex items-center justify-center">
                                            <div class="absolute w-12 h-12 bg-indigo-400 rounded-full animate-ping opacity-75"></div>
                                            <div class="relative w-8 h-8 bg-indigo-600 rounded-full border-2 border-white shadow-lg flex items-center justify-center z-10">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                            </div>
                                        </div>
                                        <div class="bg-white px-2 py-1 rounded text-xs font-bold shadow-md mt-1 text-gray-800">{{ Str::words($driver->name, 1, '') }}</div>
                                    </div>
                                    
                                    <!-- Order Marker (Bouncing) -->
                                    <div class="absolute z-10 bottom-1/4 right-1/3 transform flex flex-col items-center animate-bounce">
                                        <div class="text-red-500">
                                            <svg class="w-8 h-8 drop-shadow-md" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                                        </div>
                                        <div class="bg-white px-2 py-1 rounded text-xs font-bold shadow-md -mt-1 text-gray-800">Delivery #1002</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: Financial -->
                            <div id="content-financial" class="tab-content hidden-el">
                                <!-- Summary Cards -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                    <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 shadow-sm">
                                        <p class="text-sm font-medium text-indigo-800 mb-1">Total Earnings</p>
                                        <h4 class="text-2xl font-bold text-indigo-900">$4,520.00</h4>
                                    </div>
                                    <div class="bg-green-50 border border-green-100 rounded-xl p-4 shadow-sm">
                                        <p class="text-sm font-medium text-green-800 mb-1">Withdrawn Amount</p>
                                        <h4 class="text-2xl font-bold text-green-900">$3,200.00</h4>
                                    </div>
                                    <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 shadow-sm">
                                        <p class="text-sm font-medium text-amber-800 mb-1">Pending Balance</p>
                                        <h4 class="text-2xl font-bold text-amber-900 flex items-center justify-between">
                                            $1,320.00
                                            <button class="text-xs bg-amber-600 hover:bg-amber-700 text-white px-2 py-1 rounded transition-colors focus:outline-none" onclick="processPayout()">Payout</button>
                                        </h4>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-bold text-gray-900">Recent Transactions</h3>
                                    <div class="flex space-x-2">
                                        <button class="px-3 py-1 text-xs font-medium rounded-full bg-primary text-white focus:outline-none">Daily</button>
                                        <button class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 focus:outline-none">Weekly</button>
                                        <button class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 focus:outline-none">Monthly</button>
                                    </div>
                                </div>
                                <div class="overflow-x-auto w-full border border-gray-200 rounded-lg">
                                    <table class="w-full whitespace-nowrap text-left text-sm text-gray-500">
                                        <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200">
                                            <tr>
                                                <th scope="col" class="px-6 py-3">Date</th>
                                                <th scope="col" class="px-6 py-3">Order ID</th>
                                                <th scope="col" class="px-6 py-3 text-right">Order Amount</th>
                                                <th scope="col" class="px-6 py-3 text-right">Commission (10%)</th>
                                                <th scope="col" class="px-6 py-3 text-right">Net Earning</th>
                                            </tr>
                                        </thead>
                                        <tbody id="financialTableBody" class="divide-y divide-gray-200 bg-white">
                                            <!-- Populated via JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab: Reviews -->
                            <div id="content-reviews" class="tab-content hidden-el">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <!-- Rating Summary -->
                                    <div class="md:col-span-1">
                                        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 text-center shadow-sm">
                                            <h4 class="text-gray-500 text-sm font-medium mb-2">Average Rating</h4>
                                            <p class="text-4xl font-bold text-gray-900 mb-2">4.5</p>
                                            <div class="flex justify-center text-yellow-400 mb-2">
                                                <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                                <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                                <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                                <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                                <svg class="w-6 h-6 text-gray-300 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            </div>
                                            <p class="text-sm text-gray-500">Based on 120 reviews</p>
                                        </div>
                                    </div>
                                    <!-- Reviews List -->
                                    <div class="md:col-span-2">
                                        <h3 class="text-lg font-bold text-gray-900 mb-4">Customer Reviews</h3>
                                        <div class="space-y-4" id="reviewsContainer">
                                            <!-- Populated via JS -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: Documents -->
                            <div id="content-documents" class="tab-content hidden-el">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Driver Credentials & Documents</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <!-- License -->
                                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow group relative">
                                        <span class="absolute top-6 right-6 z-10 inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800 shadow-sm border border-green-200">Verified</span>
                                        <div class="aspect-w-16 aspect-h-10 w-full mb-3 overflow-hidden rounded-lg bg-gray-200 cursor-pointer" onclick="openImageModal('https://images.unsplash.com/photo-1633613286991-611fe299c4be?auto=format&fit=crop&w=600&q=80', 'Driver License')">
                                            <img src="https://images.unsplash.com/photo-1633613286991-611fe299c4be?auto=format&fit=crop&w=400&q=60" alt="License" class="object-cover w-full h-40 group-hover:scale-105 transition-transform duration-300">
                                        </div>
                                        <p class="text-sm font-bold text-gray-900 mb-2">Driving License</p>
                                        <div class="flex space-x-2">
                                            <button class="flex-1 px-3 py-1.5 text-xs font-bold text-white bg-green-500 hover:bg-green-600 rounded drop-shadow-sm transition-colors focus:outline-none opacity-50 cursor-not-allowed">Approve</button>
                                            <button class="flex-1 px-3 py-1.5 text-xs font-bold text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 rounded drop-shadow-sm transition-colors focus:outline-none">Reject</button>
                                        </div>
                                    </div>
                                    <!-- ID Card -->
                                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow group relative">
                                        <span class="absolute top-6 right-6 z-10 inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-yellow-100 text-yellow-800 shadow-sm border border-yellow-200">Pending</span>
                                        <div class="aspect-w-16 aspect-h-10 w-full mb-3 overflow-hidden rounded-lg bg-gray-200 cursor-pointer" onclick="openImageModal('https://images.unsplash.com/photo-1544211151-689d2d091a18?auto=format&fit=crop&w=600&q=80', 'National ID Card')">
                                            <img src="https://images.unsplash.com/photo-1544211151-689d2d091a18?auto=format&fit=crop&w=400&q=60" alt="ID Card" class="object-cover w-full h-40 group-hover:scale-105 transition-transform duration-300">
                                        </div>
                                        <p class="text-sm font-bold text-gray-900 mb-2">National ID</p>
                                        <div class="flex space-x-2">
                                            <button class="flex-1 px-3 py-1.5 text-xs font-bold text-white bg-green-500 hover:bg-green-600 rounded drop-shadow-sm transition-colors focus:outline-none">Approve</button>
                                            <button class="flex-1 px-3 py-1.5 text-xs font-bold text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 rounded drop-shadow-sm transition-colors focus:outline-none">Reject</button>
                                        </div>
                                    </div>
                                    <!-- Vehicle Registration -->
                                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow group relative">
                                        <span class="absolute top-6 right-6 z-10 inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800 shadow-sm border border-green-200">Verified</span>
                                        <div class="aspect-w-16 aspect-h-10 w-full mb-3 overflow-hidden rounded-lg bg-gray-200 cursor-pointer" onclick="openImageModal('https://images.unsplash.com/photo-1617871217743-1b9136125026?auto=format&fit=crop&w=600&q=80', 'Vehicle Registration')">
                                            <img src="https://images.unsplash.com/photo-1617871217743-1b9136125026?auto=format&fit=crop&w=400&q=60" alt="Vehicle Reg" class="object-cover w-full h-40 group-hover:scale-105 transition-transform duration-300">
                                        </div>
                                        <p class="text-sm font-bold text-gray-900 mb-2">Vehicle Registration</p>
                                        <div class="flex space-x-2">
                                            <button class="flex-1 px-3 py-1.5 text-xs font-bold text-white bg-green-500 hover:bg-green-600 rounded drop-shadow-sm transition-colors focus:outline-none opacity-50 cursor-not-allowed">Approve</button>
                                            <button class="flex-1 px-3 py-1.5 text-xs font-bold text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 rounded drop-shadow-sm transition-colors focus:outline-none">Reject</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab: Activity -->
                            <div id="content-activity" class="tab-content hidden-el">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Driver Activity Log</h3>
                                <div class="relative pl-4 border-l-2 border-indigo-200 space-y-6">
                                    <div class="relative">
                                        <div class="absolute -left-[25px] top-1 w-4 h-4 rounded-full bg-green-500 border-2 border-white shadow"></div>
                                        <p class="text-sm text-gray-500 mb-1">Today, 2:45 PM</p>
                                        <h4 class="text-md font-bold text-gray-900">Completed Order #1002</h4>
                                        <p class="text-sm text-gray-600">Delivered successfully to Ali S. in Al Olaya</p>
                                    </div>
                                    <div class="relative">
                                        <div class="absolute -left-[25px] top-1 w-4 h-4 rounded-full bg-blue-500 border-2 border-white shadow"></div>
                                        <p class="text-sm text-gray-500 mb-1">Today, 2:10 PM</p>
                                        <h4 class="text-md font-bold text-gray-900">Picked up Order #1002</h4>
                                        <p class="text-sm text-gray-600">From Burger King branch</p>
                                    </div>
                                    <div class="relative">
                                        <div class="absolute -left-[25px] top-1 w-4 h-4 rounded-full bg-indigo-500 border-2 border-white shadow"></div>
                                        <p class="text-sm text-gray-500 mb-1">Today, 1:00 PM</p>
                                        <h4 class="text-md font-bold text-gray-900">Driver Logged In</h4>
                                        <p class="text-sm text-gray-600">App version 2.4.1</p>
                                    </div>
                                    <div class="relative">
                                        <div class="absolute -left-[25px] top-1 w-4 h-4 rounded-full bg-yellow-500 border-2 border-white shadow"></div>
                                        <p class="text-sm text-gray-500 mb-1">Yesterday, 9:30 AM</p>
                                        <h4 class="text-md font-bold text-gray-900">Modified Profile Settings</h4>
                                        <p class="text-sm text-gray-600">Changed driving vehicle to Toyota Corolla 2021</p>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Modals -->

    <!-- Order Timeline Modal -->
    <div id="timelineModal" class="relative z-50 hidden-el" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md modal-content">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900">Timeline: <span id="timelineOrderId" class="text-indigo-600"></span></h3>
                        <button onclick="closeModal('timelineModal')" class="text-gray-400 hover:text-gray-600 p-1 focus:outline-none">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="relative border-l-2 border-indigo-200 pl-4 space-y-6">
                            <div class="relative">
                                <div class="absolute -left-[25px] top-1 w-4 h-4 rounded-full bg-indigo-500 border-2 border-white"></div>
                                <p class="text-xs text-gray-500">14:05 PM</p>
                                <h4 class="text-sm font-bold text-gray-900">Order Created</h4>
                            </div>
                            <div class="relative">
                                <div class="absolute -left-[25px] top-1 w-4 h-4 rounded-full bg-blue-500 border-2 border-white"></div>
                                <p class="text-xs text-gray-500">14:12 PM</p>
                                <h4 class="text-sm font-bold text-gray-900">Driver Accepted</h4>
                            </div>
                            <div class="relative">
                                <div class="absolute -left-[25px] top-1 w-4 h-4 rounded-full bg-yellow-500 border-2 border-white"></div>
                                <p class="text-xs text-gray-500">14:20 PM</p>
                                <h4 class="text-sm font-bold text-gray-900">Order Picked Up</h4>
                            </div>
                            <div class="relative">
                                <div class="absolute -left-[25px] top-1 w-4 h-4 rounded-full bg-green-500 border-2 border-white"></div>
                                <p class="text-xs text-gray-500">14:30 PM</p>
                                <h4 class="text-sm font-bold text-gray-900">Order Delivered</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Send Notification Modal -->
    <div id="notificationModal" class="relative z-50 hidden-el" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-content">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4">Send Instant Notification</h3>
                        <form id="notificationForm" onsubmit="sendNotification(event)">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Title</label>
                                    <input type="text" id="notifTitle" class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-sm" placeholder="Message Title" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Message</label>
                                    <textarea id="notifMessage" rows="4" class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-sm" placeholder="Type notification content..." required></textarea>
                                </div>
                            </div>
                            <div class="mt-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="inline-flex w-full justify-center rounded-md bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary_dark sm:ml-3 sm:w-auto">Send Now</button>
                                <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" onclick="closeModal('notificationModal')">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div id="imageModal" class="relative z-50 hidden-el" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:max-w-3xl w-full modal-content">
                    <div class="flex justify-between items-center p-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900" id="imageModalTitle">Document Preview</h3>
                        <button onclick="closeModal('imageModal')" class="text-gray-400 hover:text-gray-600 focus:outline-none bg-gray-100 rounded-full p-1 transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="p-4 bg-gray-100 flex justify-center max-h-[70vh] overflow-hidden">
                        <img id="imageModalSrc" src="" alt="Document Preview" class="max-w-full max-h-full object-contain shadow-sm border border-gray-300 rounded">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div id="toastSuccess" class="fixed top-5 right-5 z-50 flex items-center gap-3 bg-white border border-green-200 shadow-lg rounded-xl px-5 py-4 min-w-[280px]" role="alert">
        <div class="flex-shrink-0 w-9 h-9 rounded-full bg-green-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div class="flex-1">
            <p class="text-sm font-semibold text-green-800">Success</p>
            <p class="text-xs text-green-600 mt-0.5">{{ session('success') }}</p>
        </div>
        <button onclick="document.getElementById('toastSuccess').remove()" class="text-gray-400 hover:text-gray-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <script>setTimeout(() => { const t = document.getElementById('toastSuccess'); if(t) t.remove(); }, 4000);</script>
    @endif

    <!-- Driver Data for JS (passed as JSON) -->
    @php
        $driverData = [
            'id'           => $driver->id,
            'name'         => $driver->name,
            'phone'        => $driver->phone,
            'email'        => $driver->email,
            'status'       => $statusLabel,
            'idNumber'     => $profile->id_number    ?? null,
            'address'      => $profile->address       ?? null,
            'vehicleModel' => $profile->vehicle_model ?? null,
            'vehiclePlate' => $profile->vehicle_plate ?? null,
            'vehicleVin'   => $profile->vehicle_vin   ?? null,
            'avatar'       => $profile->avatar_url    ?? null,
        ];
    @endphp
    <script>
        const DRIVER_DATA = @json($driverData);
        const DRIVER_TOGGLE_AVAILABILITY_URL = '{{ url('admin/drivers') }}';

        function showToastError(msg) {
            const toast = document.createElement('div');
            toast.className = 'fixed top-5 right-5 z-50 flex items-center gap-3 bg-white border border-red-200 shadow-lg rounded-xl px-5 py-4 min-w-[280px] animate-fade-in-up';
            toast.innerHTML = `
                <div class="flex-shrink-0 w-9 h-9 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-red-800">Error</p>
                    <p class="text-xs text-red-600 mt-0.5">${msg}</p>
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        }

        function toggleDetailsAvailability(driverId, btnEl) {
            if (btnEl.disabled) return;
            btnEl.disabled = true;
            
            var originalClass = btnEl.className;
            var originalHtml = btnEl.innerHTML;
            
            btnEl.classList.add('opacity-50', 'cursor-wait');
            
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                          || document.querySelector('input[name="_token"]')?.value 
                          || window.Laravel?.csrfToken;

            if (!token) { 
                console.error("CSRF token not found!"); 
                showToastError("Security token missing. Please refresh the page."); 
                btnEl.classList.remove('opacity-50', 'cursor-wait');
                btnEl.disabled = false;
                return; 
            }

            var url = '/admin/drivers/toggle-availability/' + driverId;
            console.log('Sending request to:', url);

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                }
            })
            .then(response => {
                return response.text().then(text => {
                    let data;
                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        alert("Server returned non-JSON response:\n" + text.substring(0, 500));
                        throw new Error("Invalid JSON response");
                    }
                    if (!response.ok) {
                        throw new Error(data.message || 'Server Error');
                    }
                    return data;
                });
            })
            .then(data => {
                if (data.success || data.is_online !== undefined) {
                    const isOnline = data.is_online;
                    if(isOnline) {
                        btnEl.className = "inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border focus:outline-none transition-all duration-200 border-green-200 bg-green-50 text-green-800 hover:bg-green-100";
                        btnEl.innerHTML = '<span class="availability-dot w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> <span class="availability-text font-medium">Online</span>';
                    } else {
                        btnEl.className = "inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border focus:outline-none transition-all duration-200 border-red-200 bg-red-50 text-red-800 hover:bg-red-100";
                        btnEl.innerHTML = '<span class="availability-dot w-2 h-2 rounded-full bg-red-500"></span> <span class="availability-text font-medium">Offline</span>';
                    }
                } else {
                    showToastError(data.error || data.message || 'Error occurred while updating availability.');
                    btnEl.className = originalClass;
                    btnEl.innerHTML = originalHtml;
                }
            })
            .catch(err => {
                console.error('[driver-details.blade] toggleDetailsAvailability error:', err);
                if (err.message !== "Invalid JSON response") {
                    showToastError('Error: ' + err.message);
                }
                btnEl.className = originalClass;
                btnEl.innerHTML = originalHtml;
            })
            .finally(() => {
                btnEl.classList.remove('opacity-50', 'cursor-wait');
                btnEl.disabled = false;
            });
        }
    </script>

    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    <script src="{{ asset('modules/admin/js/driver-details.js') }}"></script>
</body>
</html>
