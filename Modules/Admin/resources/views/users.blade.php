<!DOCTYPE html>
<html lang="en">
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
                    <a href="{{ route('admin.users.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-primary bg-indigo-50 border-l-4 border-primary px-6 rounded-r-lg group transition-colors">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
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
        <header class="bg-white shadow-sm ring-1 ring-gray-200 z-10">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                <!-- Hamburger Menu -->
                <div class="flex items-center flex-1">
                    <button id="mobileMenuBtn" class="text-gray-500 focus:outline-none lg:hidden pl-1 pr-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <!-- Search Input for Navbar -->
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
                        <!-- Dropdown Menu -->
                        <div id="profileDropdownMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 z-20">
                            <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Your Profile</a>
                            <a href="{{ route('admin.settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                            <div class="border-t border-gray-100"></div>
                            <form method="POST" action="{{ route('admin.logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Sign out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Toast Flash Messages -->
        @if(session('success'))
        <div id="toastSuccess" class="fixed top-5 right-5 z-50 flex items-center gap-3 bg-white border border-green-200 shadow-lg rounded-xl px-5 py-4 min-w-[280px] animate-pulse" role="alert">
            <div class="flex-shrink-0 w-9 h-9 rounded-full bg-green-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-green-800">Success</p>
                <p class="text-xs text-green-600 mt-0.5">{{ session('success') }}</p>
            </div>
            <button onclick="document.getElementById('toastSuccess').remove()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <script>setTimeout(() => { const t = document.getElementById('toastSuccess'); if(t) t.remove(); }, 4000);</script>
        @endif

        @if(session('error'))
        <div id="toastError" class="fixed top-5 right-5 z-50 flex items-center gap-3 bg-white border border-red-200 shadow-lg rounded-xl px-5 py-4 min-w-[280px]" role="alert">
            <div class="flex-shrink-0 w-9 h-9 rounded-full bg-red-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-red-800">Error</p>
                <p class="text-xs text-red-600 mt-0.5">{{ session('error') }}</p>
            </div>
            <button onclick="document.getElementById('toastError').remove()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <script>setTimeout(() => { const t = document.getElementById('toastError'); if(t) t.remove(); }, 4000);</script>
        @endif

        <!-- Main Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto w-full">
            
            <!-- Page Header -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Users Management</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage platform administrators and customers.</p>
                </div>
                <button onclick="openModal('userModal')" class="bg-primary hover:bg-primary_dark text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    <span>Add User</span>
                </button>
            </div>

            {{-- Inline Validation Errors (persist modal open on next render) --}}
            @if($errors->any())
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    <div>
                        <p class="text-sm font-semibold text-red-800 mb-1">Please fix the following errors:</p>
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
                <div class="p-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0">
                    <div class="relative w-full sm:max-w-xs">
                        <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none pl-3">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" id="userSearch" placeholder="Search users by name, email..." class="w-full h-9 pl-9 pr-3 rounded-md border border-gray-300 bg-white text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm">
                    </div>
                </div>

                <!-- Table Wrapper -->
                <div class="overflow-x-auto w-full">
                    <table class="w-full whitespace-nowrap text-left text-sm text-gray-500">
                        <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-3">ID</th>
                                <th scope="col" class="px-6 py-3">Name</th>
                                <th scope="col" class="px-6 py-3">Email</th>
                                <th scope="col" class="px-6 py-3">Phone</th>
                                <th scope="col" class="px-6 py-3">Role</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody" class="divide-y divide-gray-200 bg-white">
                            @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-xs font-medium text-gray-900">#{{ $user->id }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img class="h-8 w-8 rounded-full border border-gray-200 mr-3" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" alt="">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $user->phone ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $user->roles->first()?->name ?? 'Customer' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ strtolower($user->status) === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $user->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                                    @php
                                        $viewData = [
                                            'id'         => $user->id,
                                            'name'       => $user->name,
                                            'email'      => $user->email,
                                            'phone'      => $user->phone,
                                            'role'       => $user->roles->first()?->name ?? 'Customer',
                                            'status'     => $user->status,
                                            'created_at' => $user->created_at->format('d M Y, H:i'),
                                        ];
                                    @endphp
                                    <button type="button"
                                        onclick="openViewModal({{ json_encode($viewData) }})"
                                        class="text-indigo-600 hover:text-indigo-900" title="View User">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    <button type="button" onclick="openEditModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}', '{{ $user->phone }}', '{{ $user->roles->first()?->name ?? 'Customer' }}', '{{ $user->status }}')" class="text-blue-600 hover:text-blue-900" title="Edit"><svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                                    <button type="button" onclick="openDeleteModal({{ $user->id }})" class="text-red-600 hover:text-red-900" title="Delete"><svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No users found.</td>
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
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md modal-content">
                    <form id="userForm" method="POST" action="{{ route('admin.users.store') }}" data-store-url="{{ route('admin.users.store') }}">
                        @csrf
                        <div id="methodContainer"></div>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-gray-100">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">Add User</h3>
                                    
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
                                            <label class="block text-sm font-medium text-gray-700">Full Name</label>
                                            <input type="text" id="userName" name="name" class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-sm" required>
                                        </div>
                                        <div class="mt-3">
                                            <label class="block text-sm font-medium text-gray-700">Email Address</label>
                                            <input type="email" id="userEmail" name="email" class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-sm" required>
                                        </div>
                                        <div class="mt-3">
                                            <label class="block text-sm font-medium text-gray-700">Phone</label>
                                            <input type="tel" id="userPhone" name="phone" class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-sm">
                                        </div>
                                        <div class="mt-3">
                                            <label class="block text-sm font-medium text-gray-700">Password</label>
                                            <input type="password" id="userPassword" name="password" placeholder="Auto-generated if empty" class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-sm">
                                        </div>
                                        <div class="mt-3">
                                            <label class="block text-sm font-medium text-gray-700">Role</label>
                                            <select id="userRole" name="role" class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-sm">
                                                <option value="Customer">Customer</option>
                                                <option value="System Admin">System Admin</option>
                                            </select>
                                        </div>
                                        <div class="mt-3">
                                            <label class="block text-sm font-medium text-gray-700">Status</label>
                                            <select id="userStatus" name="status" class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-sm">
                                                <option value="Active">Active</option>
                                                <option value="Blocked">Blocked</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit" class="inline-flex w-full justify-center rounded-md bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary_dark sm:ml-3 sm:w-auto">Save User</button>
                            <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" onclick="closeModal('userModal')">Cancel</button>
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
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md modal-content">
                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">Delete User</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">Are you sure you want to delete this user? All of their data will be permanently removed. This action cannot be undone.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">Delete</button>
                            <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" onclick="closeModal('deleteModal')">Cancel</button>
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
                <div class="relative transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all w-full sm:max-w-lg">

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-white">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900">User Profile</h3>
                        </div>
                        <button onclick="closeModal('viewUserModal')" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Avatar + Name --}}
                    <div class="flex flex-col items-center py-6 px-6 border-b border-gray-100 bg-gray-50">
                        <img id="view_avatar" src="" alt="" class="w-20 h-20 rounded-full border-4 border-white shadow-md object-cover">
                        <h4 id="view_name" class="mt-3 text-lg font-bold text-gray-900">—</h4>
                        <p id="view_email" class="text-sm text-gray-500 mt-0.5">—</p>
                        <div class="mt-2 flex items-center gap-2">
                            <span id="view_role_badge" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">—</span>
                            <span id="view_status_badge" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">—</span>
                        </div>
                    </div>

                    {{-- Details grid --}}
                    <div class="divide-y divide-gray-50 px-6">
                        <div class="grid grid-cols-3 py-3">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide col-span-1">User ID</span>
                            <span id="view_id" class="text-sm text-gray-800 col-span-2 font-mono">#—</span>
                        </div>
                        <div class="grid grid-cols-3 py-3">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide col-span-1">Phone</span>
                            <span id="view_phone" class="text-sm text-gray-800 col-span-2">—</span>
                        </div>
                        <div class="grid grid-cols-3 py-3">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide col-span-1">Status</span>
                            <span id="view_status" class="text-sm text-gray-800 col-span-2">—</span>
                        </div>
                        <div class="grid grid-cols-3 py-3">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide col-span-1">Role</span>
                            <span id="view_role" class="text-sm text-gray-800 col-span-2">—</span>
                        </div>
                        <div class="grid grid-cols-3 py-3">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide col-span-1">Joined</span>
                            <span id="view_created_at" class="text-sm text-gray-800 col-span-2">—</span>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 bg-gray-50 flex items-center justify-between border-t border-gray-100">
                        <a id="view_profile_link" href="#" class="text-sm text-primary hover:underline font-medium">View Full Profile →</a>
                        <button onclick="closeModal('viewUserModal')" class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Close</button>
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
        document.addEventListener('DOMContentLoaded', function() {
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
