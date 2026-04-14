<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - Admin Dashboard</title>
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
                        danger: '#ef4444',
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
                    <a href="{{ route('admin.settings.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-primary bg-indigo-50 border-l-4 border-primary px-6 rounded-r-lg group transition-colors">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
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
                    <h2 class="hidden md:block text-lg font-bold text-gray-900 py-2">Master Configuration</h2>
                </div>

                <!-- Right Nav Items -->
                <div class="flex items-center space-x-4">
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
        <main class="flex-1 overflow-y-auto w-full bg-gray-50 flex flex-col">
            
            <!-- Sub Navigation Tabs for Settings using DOM toggling -->
            <div class="bg-white border-b border-gray-200 px-4 sm:px-6 lg:px-8 pt-4">
                <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
                    <button onclick="switchTab('tab-app')" id="btn-tab-app" class="border-primary text-primary whitespace-nowrap border-b-2 py-4 px-1 text-sm font-bold focus:outline-none">
                        App Config
                    </button>
                    <button onclick="switchTab('tab-notif')" id="btn-tab-notif" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors focus:outline-none">
                        Notifications Config
                    </button>
                    <button onclick="switchTab('tab-terms')" id="btn-tab-terms" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors focus:outline-none">
                        Terms & Conditions
                    </button>
                    <button onclick="switchTab('tab-privacy')" id="btn-tab-privacy" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors focus:outline-none">
                        Privacy Policy
                    </button>
                </nav>
            </div>

            <!-- Toast Alert -->
            <div id="toast" class="hidden-el fixed bottom-4 right-4 z-50 rounded-md bg-green-50 p-4 shadow-lg border border-green-200 transition-opacity duration-300">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400 border-green-200" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800" id="toastMessage">Success</p>
                    </div>
                </div>
            </div>

            <!-- Tab Content Wrapper -->
            <div class="p-4 sm:p-6 lg:p-8 flex-1">
                
                <!-- TAB 1: App Config -->
                <div id="tab-app" class="max-w-4xl mx-auto space-y-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Application Configuration</h2>
                        <p class="text-sm text-gray-500">Global string parameters powering the front-end user experience.</p>
                    </div>

                    <div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-xl overflow-hidden divide-y divide-gray-100">
                        <!-- Key/Value Row -->
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="w-1/2">
                                <h4 class="text-sm font-semibold text-gray-900">App Name</h4>
                                <p class="text-xs text-gray-500">Displayed on splash screens and emails.</p>
                            </div>
                            <div class="w-1/3 text-right font-medium text-gray-700" id="val-appName">
                                FoodDelivery Pro
                            </div>
                            <button onclick="openEditModal('App Name', 'val-appName')" class="ml-4 text-indigo-600 hover:text-indigo-900 text-sm font-bold">Edit</button>
                        </div>

                        <!-- Key/Value Row -->
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="w-1/2">
                                <h4 class="text-sm font-semibold text-gray-900">Support Email</h4>
                                <p class="text-xs text-gray-500">Default fallback email for user dispute tickets.</p>
                            </div>
                            <div class="w-1/3 text-right font-medium text-gray-700" id="val-supEmail">
                                help@fooddelivery.app
                            </div>
                            <button onclick="openEditModal('Support Email', 'val-supEmail')" class="ml-4 text-indigo-600 hover:text-indigo-900 text-sm font-bold">Edit</button>
                        </div>

                        <!-- Key/Value Row -->
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="w-1/2">
                                <h4 class="text-sm font-semibold text-gray-900">Global Currency Symbol</h4>
                                <p class="text-xs text-gray-500">Injected into prefix span classes dynamically.</p>
                            </div>
                            <div class="w-1/3 text-right font-medium text-gray-700" id="val-currency">
                                $
                            </div>
                            <button onclick="openEditModal('Global Currency Symbol', 'val-currency')" class="ml-4 text-indigo-600 hover:text-indigo-900 text-sm font-bold">Edit</button>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: Notifications Config -->
                <div id="tab-notif" class="max-w-4xl mx-auto space-y-6 hidden-el">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">API Notifications Config</h2>
                        <p class="text-sm text-gray-500">SMTP and Auth keys for OneSignal Push hooks.</p>
                    </div>

                    <div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-xl overflow-hidden divide-y divide-gray-100">
                        <!-- Key/Value Row -->
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="w-1/2">
                                <h4 class="text-sm font-semibold text-gray-900">OneSignal App ID</h4>
                                <p class="text-xs text-gray-500">Required payload for push broadcasts to phones.</p>
                            </div>
                            <div class="w-1/3 text-right font-medium text-gray-500 italic" id="val-oneSigId">
                                d3f4j-****-****
                            </div>
                            <button onclick="openEditModal('OneSignal App ID', 'val-oneSigId')" class="ml-4 text-indigo-600 hover:text-indigo-900 text-sm font-bold">Edit</button>
                        </div>

                        <!-- Key/Value Row -->
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="w-1/2">
                                <h4 class="text-sm font-semibold text-gray-900">SMTP Host (SendGrid)</h4>
                                <p class="text-xs text-gray-500">Mailing server endpoint URL.</p>
                            </div>
                            <div class="w-1/3 text-right font-medium text-gray-700" id="val-smtp">
                                smtp.sendgrid.net
                            </div>
                            <button onclick="openEditModal('SMTP Host (SendGrid)', 'val-smtp')" class="ml-4 text-indigo-600 hover:text-indigo-900 text-sm font-bold">Edit</button>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: Terms & Conditions -->
                <div id="tab-terms" class="max-w-4xl mx-auto space-y-6 hidden-el">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Terms & Conditions</h2>
                            <p class="text-sm text-gray-500">Legal formatting rendered dynamically in the mobile app web-view.</p>
                        </div>
                        <button onclick="openTextModal('Terms & Conditions', 'val-termsBody')" class="bg-primary text-white text-sm font-bold py-2 px-4 rounded shadow-sm hover:bg-primary_dark transition-colors">Edit Terms</button>
                    </div>

                    <div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-xl overflow-hidden p-6 text-sm text-gray-600 leading-relaxed font-sans" style="white-space: pre-wrap;" id="val-termsBody">
Welcome to FoodDelivery Pro. 

By accessing this platform, you agree to comply with our binding terms...

1. Order Liabilities:
The platform is not responsible for food temperature upon arrival. Refunds are strictly mediated by our complaints department and subject to an internal 24 hour review cycle.

2. Driver Guidelines:
Drivers must retain an active background check within the past 12 months.
                    </div>
                </div>

                <!-- TAB 4: Privacy Policy -->
                <div id="tab-privacy" class="max-w-4xl mx-auto space-y-6 hidden-el">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Privacy Policy</h2>
                            <p class="text-sm text-gray-500">Governing data capture standards for GDPR compliance.</p>
                        </div>
                        <button onclick="openTextModal('Privacy Policy', 'val-privBody')" class="bg-primary text-white text-sm font-bold py-2 px-4 rounded shadow-sm hover:bg-primary_dark transition-colors">Edit Policy</button>
                    </div>

                    <div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-xl overflow-hidden p-6 text-sm text-gray-600 leading-relaxed font-sans" style="white-space: pre-wrap;" id="val-privBody">
Data Request Limits:
We retain transactional delivery mapping metadata for up to 90 days. 

Personally Identifiable Information revolves exclusively around IP Tracking for security and Payment gateways natively encrypted via Stripe Tokens. We literally store zero raw cards in our active db schemas.
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Edit Short String Modal -->
    <div id="editStringModal" class="relative z-40 hidden-el" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md modal-content">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900" id="modalStringTitle">Edit Key</h3>
                        <button onclick="closeModals()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    
                    <form onsubmit="handleStringSave(event)">
                        <input type="hidden" id="targetIdRef">
                        <div class="px-6 py-5">
                            <label for="stringValInput" class="block text-sm font-medium text-gray-700 mb-1">New Value</label>
                            <input type="text" id="stringValInput" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-10 px-3 border border-gray-200">
                        </div>
                        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                            <button type="button" onclick="closeModals()" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">Cancel</button>
                            <button type="submit" class="bg-primary text-white px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-primary_dark transition-colors">Update Setting</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Long Text block Modal -->
    <div id="editTextModal" class="relative z-40 hidden-el" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl modal-content">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900" id="modalTextTitle">Edit Body</h3>
                        <button onclick="closeModals()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    
                    <form onsubmit="handleTextSave(event)">
                        <input type="hidden" id="targetTextRef">
                        <div class="px-6 py-5">
                            <label for="textValInput" class="block text-sm font-medium text-gray-700 mb-1">Markdown / Plaintext Supported</label>
                            <textarea id="textValInput" required rows="12" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-3 border border-gray-200"></textarea>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                            <button type="button" onclick="closeModals()" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 transition-colors">Cancel</button>
                            <button type="submit" class="bg-primary text-white px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-primary_dark transition-colors">Publish Document</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    <script src="{{ asset('modules/admin/js/settings.js') }}"></script>
</body>
</html>
