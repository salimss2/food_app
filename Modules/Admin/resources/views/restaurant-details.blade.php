<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Details - Admin Dashboard</title>
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
                <div class="flex items-center flex-1">
                    <button id="mobileMenuBtn" class="text-gray-500 focus:outline-none lg:hidden pl-1 pr-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <div class="hidden md:flex items-center">
                        <a href="{{ route('admin.restaurants.index') }}" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Back to Restaurants
                        </a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="relative p-2 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                        <span class="absolute top-1.5 right-1.5 block w-2 h-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </button>
                    <div class="relative">
                        <button id="profileDropdownBtn" class="flex items-center space-x-2 focus:outline-none">
                            <img class="w-8 h-8 rounded-full border-2 border-primary object-cover" src="https://ui-avatars.com/api/?name=Admin+User&background=4f46e5&color=fff" alt="Admin avatar">
                            <span class="hidden md:block font-medium text-sm text-gray-700">Admin</span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto w-full relative" data-id="123">
            
            <!-- Header Section (Top Card) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex flex-col lg:flex-row justify-between items-start gap-6">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 w-full lg:w-auto">
                        <div class="relative">
                            <img class="w-24 h-24 rounded-xl border-4 border-indigo-50 shadow-md object-cover" src="https://images.unsplash.com/photo-1517248135467-4c7ed9d74c71?w=200&h=200&fit=crop" alt="Restaurant logo">
                            <div class="absolute -bottom-2 -right-2 bg-green-500 w-6 h-6 rounded-full border-2 border-white shadow-sm flex items-center justify-center" title="Open Now">
                                <span class="w-2 h-2 rounded-full bg-white"></span>
                            </div>
                        </div>
                        <div class="text-center sm:text-left">
                            <h2 class="text-2xl font-bold text-gray-900">The Gourmet Kitchen</h2>
                            <div class="flex items-center justify-center sm:justify-start gap-3 mt-1">
                                <div class="flex items-center text-yellow-400">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span class="ml-1 text-sm font-semibold text-gray-700">4.8</span>
                                </div>
                                <span class="text-sm text-gray-500">(2,450 Reviews)</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Premium Partner</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-2">123 Culinary St, Food City, FC 90210</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-4 w-full lg:w-auto">
                        <!-- KPI Mini Cards in Header -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 w-full">
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Gross Sales</p>
                                <p class="text-lg font-bold text-gray-900">$45,230</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Net Profit</p>
                                <p class="text-lg font-bold text-green-600">$38,445</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Orders</p>
                                <p class="text-lg font-bold text-gray-900">1,842</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Top Item</p>
                                <p class="text-lg font-bold text-indigo-600">Truffle Pizza</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col xl:flex-row gap-6">
                <!-- Left Sidebar Panel (Mini Card) -->
                <div class="w-full xl:w-1/4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sticky top-20">
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b pb-2">Account Summary</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider">Activity Status</p>
                                <div class="mt-1 flex items-center">
                                    <div class="w-2 h-2 rounded-full bg-green-500 mr-2"></div>
                                    <span class="font-medium text-sm text-gray-900">Active</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider">Store Status</p>
                                <div class="mt-1 flex items-center">
                                    <div class="w-2 h-2 rounded-full bg-blue-500 mr-2"></div>
                                    <span class="font-medium text-sm text-gray-900">Open for Orders</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider">Prep Time (Avg)</p>
                                <p class="mt-1 font-semibold text-lg text-gray-900">18-22 mins</p>
                            </div>
                            
                            <div class="pt-4 border-t border-gray-100">
                                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Quick Actions</h3>
                                <div class="space-y-2">
                                    <button onclick="openModal('notificationModal')" class="w-full flex items-center px-3 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary_dark transition shadow-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                        Send Notification
                                    </button>
                                    <button onclick="openModal('commissionModal')" class="w-full flex items-center px-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path></svg>
                                        Change Commission
                                    </button>
                                    <button onclick="passwordReset()" class="w-full flex items-center px-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                        Reset Password
                                    </button>
                                    <button onclick="openModal('blockModal')" class="w-full flex items-center px-3 py-2 border border-red-100 text-red-600 rounded-lg text-sm font-medium hover:bg-red-50 transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                        Temporary Block
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content (Tabs System) -->
                <div class="w-full xl:w-3/4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        
                        <!-- Tabs Header -->
                        <div class="flex overflow-x-auto border-b border-gray-200 hide-scrollbar scroll-smooth">
                            <button onclick="switchTab('metrics')" id="tab-metrics" class="tab-btn active shrink-0 px-6 py-4 text-sm font-medium border-b-2 border-transparent transition-all focus:outline-none">
                                Performance Metrics
                            </button>
                            <button onclick="switchTab('financials')" id="tab-financials" class="tab-btn shrink-0 px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent transition-all focus:outline-none">
                                Financials
                            </button>
                            <button onclick="switchTab('menu')" id="tab-menu" class="tab-btn shrink-0 px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent transition-all focus:outline-none">
                                Menu Preview
                            </button>
                            <button onclick="switchTab('reviews')" id="tab-reviews" class="tab-btn shrink-0 px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent transition-all focus:outline-none">
                                Reviews & Feedback
                            </button>
                            <button onclick="switchTab('logistics')" id="tab-logistics" class="tab-btn shrink-0 px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent transition-all focus:outline-none">
                                Logistics & Map
                            </button>
                            <button onclick="switchTab('documents')" id="tab-documents" class="tab-btn shrink-0 px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent transition-all focus:outline-none">
                                Legal Documents
                            </button>
                        </div>

                        <!-- Tabs Content -->
                        <div class="p-6">
                            
                            <!-- Tab: Metrics -->
                            <div id="content-metrics" class="tab-content transition-opacity duration-300">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                    <div class="p-6 rounded-xl border border-gray-100 bg-gray-50">
                                        <h4 class="text-sm font-bold text-gray-600 uppercase mb-4">Total Revenue Flow</h4>
                                        <div class="h-48 flex items-end justify-between gap-2 px-2">
                                            <!-- Simple Bar Chart Visualization -->
                                            <div class="w-full bg-indigo-200 rounded-t h-[30%]" title="Mon"></div>
                                            <div class="w-full bg-indigo-300 rounded-t h-[45%]" title="Tue"></div>
                                            <div class="w-full bg-indigo-400 rounded-t h-[60%]" title="Wed"></div>
                                            <div class="w-full bg-indigo-500 rounded-t h-[85%]" title="Thu"></div>
                                            <div class="w-full bg-indigo-600 rounded-t h-[70%]" title="Fri"></div>
                                            <div class="w-full bg-indigo-700 rounded-t h-[95%]" title="Sat"></div>
                                            <div class="w-full bg-indigo-800 rounded-t h-[80%]" title="Sun"></div>
                                        </div>
                                        <div class="flex justify-between mt-2 text-[10px] uppercase font-bold text-gray-400">
                                            <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
                                        </div>
                                    </div>
                                    <div class="p-6 rounded-xl border border-gray-100 bg-gray-50">
                                        <h4 class="text-sm font-bold text-gray-600 uppercase mb-4">Order Status Mix</h4>
                                        <div class="space-y-4 pt-2">
                                            <div class="relative pt-1">
                                                <div class="flex mb-2 items-center justify-between">
                                                    <div class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-green-600 bg-green-200">Completed</div>
                                                    <div class="text-right"><span class="text-xs font-semibold inline-block text-green-600">88%</span></div>
                                                </div>
                                                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-green-200"><div style="width:88%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500"></div></div>
                                            </div>
                                            <div class="relative pt-1">
                                                <div class="flex mb-2 items-center justify-between">
                                                    <div class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-red-600 bg-red-200">Cancelled</div>
                                                    <div class="text-right"><span class="text-xs font-semibold inline-block text-red-600">7%</span></div>
                                                </div>
                                                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-red-200"><div style="width:7%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-red-500"></div></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h4 class="text-sm font-bold text-gray-900 uppercase mb-4">Top Selling Items</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="flex items-center p-3 bg-white border border-gray-100 rounded-lg shadow-sm">
                                        <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600 font-bold mr-4">#1</div>
                                        <div><p class="font-bold text-gray-900">Truffle Mushroom Pizza</p><p class="text-xs text-gray-500">420 units sold</p></div>
                                    </div>
                                    <div class="flex items-center p-3 bg-white border border-gray-200 rounded-lg">
                                        <div class="w-12 h-12 bg-gray-50 rounded-lg flex items-center justify-center text-gray-600 font-bold mr-4">#2</div>
                                        <div><p class="font-bold text-gray-900">Wagyu Beef Burger</p><p class="text-xs text-gray-500">385 units sold</p></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: Financials -->
                            <div id="content-financials" class="tab-content hidden-el transition-opacity duration-300">
                                <div class="flex flex-col md:flex-row justify-between gap-6 mb-8">
                                    <div class="flex-1 bg-indigo-50 border border-indigo-100 rounded-xl p-5">
                                        <p class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-1">Current Balance</p>
                                        <h4 class="text-3xl font-bold text-indigo-900">$12,450.00</h4>
                                        <button class="mt-4 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-md hover:bg-indigo-700 transition">Process Withdrawal</button>
                                    </div>
                                    <div class="flex-1 bg-gray-50 border border-gray-100 rounded-xl p-5">
                                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Commission Rate</p>
                                        <div class="flex items-baseline gap-2">
                                            <h4 class="text-3xl font-bold text-gray-900">12%</h4>
                                            <span class="text-xs text-gray-500">(Custom Tier)</span>
                                        </div>
                                        <button onclick="openModal('commissionModal')" class="mt-4 text-primary text-sm font-bold flex items-center hover:underline">
                                            Edit Commission
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </button>
                                    </div>
                                </div>
                                <h4 class="text-sm font-bold text-gray-900 uppercase mb-4">Withdrawal History</h4>
                                <div class="overflow-x-auto border border-gray-100 rounded-xl">
                                    <table class="w-full text-left text-sm whitespace-nowrap">
                                        <thead class="bg-gray-50 border-b border-gray-100">
                                            <tr>
                                                <th class="px-6 py-4 font-bold text-gray-600">Reference</th>
                                                <th class="px-6 py-4 font-bold text-gray-600">Date</th>
                                                <th class="px-6 py-4 font-bold text-gray-600 text-right">Amount</th>
                                                <th class="px-6 py-4 font-bold text-gray-600">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50">
                                            <tr>
                                                <td class="px-6 py-4 font-medium">WD-45812</td>
                                                <td class="px-6 py-4 text-gray-500">Oct 24, 2023</td>
                                                <td class="px-6 py-4 text-right font-bold text-gray-900">$2,500.00</td>
                                                <td class="px-6 py-4"><span class="px-2 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 uppercase">Paid</span></td>
                                            </tr>
                                            <tr>
                                                <td class="px-6 py-4 font-medium">WD-45700</td>
                                                <td class="px-6 py-4 text-gray-500">Oct 15, 2023</td>
                                                <td class="px-6 py-4 text-right font-bold text-gray-900">$1,800.00</td>
                                                <td class="px-6 py-4"><span class="px-2 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 uppercase">Paid</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab: Menu Preview -->
                            <div id="content-menu" class="tab-content hidden-el transition-opacity duration-300">
                                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                                    <div class="inline-flex space-x-2">
                                        <button class="bg-gray-100 text-gray-900 px-3 py-1.5 rounded-full text-xs font-bold ring-1 ring-gray-200">All Items</button>
                                        <button class="text-gray-500 hover:text-gray-900 px-3 py-1.5 text-xs font-bold">Main Courses</button>
                                        <button class="text-gray-500 hover:text-gray-900 px-3 py-1.5 text-xs font-bold">Appetizers</button>
                                    </div>
                                    <div class="relative w-full sm:w-64">
                                        <input type="text" placeholder="Search menu..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all">
                                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <!-- Menu Item Card -->
                                    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                        <img src="https://images.unsplash.com/photo-1513104890138-7c749659a591?w=400&h=200&fit=crop" class="w-full h-32 object-cover">
                                        <div class="p-4">
                                            <div class="flex justify-between items-start mb-2 text-balance">
                                                <h5 class="font-bold text-gray-900">Classic Margherita</h5>
                                                <span class="text-indigo-600 font-bold">$14.99</span>
                                            </div>
                                            <p class="text-xs text-gray-500 line-clamp-2 mb-4">Fresh mozzarella, tomato sauce, and basil on a thin crust.</p>
                                            <div class="flex justify-between items-center border-t border-gray-50 pt-3">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-50 text-green-600 uppercase">Available</span>
                                                <button onclick="toggleItem(event)" class="text-[10px] font-bold text-red-600 uppercase hover:underline">Disable Item</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow opacity-60">
                                        <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=200&fit=crop" class="w-full h-32 object-cover grayscale">
                                        <div class="p-4">
                                            <div class="flex justify-between items-start mb-2">
                                                <h5 class="font-bold text-gray-900">Garden Salad</h5>
                                                <span class="text-indigo-600 font-bold">$9.50</span>
                                            </div>
                                            <p class="text-xs text-gray-500 line-clamp-2 mb-4">Seasonal greens with balsamic dressing.</p>
                                            <div class="flex justify-between items-center border-t border-gray-50 pt-3">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-600 uppercase">Disabled</span>
                                                <button onclick="toggleItem(event)" class="text-[10px] font-bold text-green-600 uppercase hover:underline">Enable Item</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: Reviews -->
                            <div id="content-reviews" class="tab-content hidden-el transition-opacity duration-300">
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                                    <div class="lg:col-span-2 space-y-6">
                                        <h4 class="text-sm font-bold text-gray-900 uppercase">Recent Customer Reviews</h4>
                                        <div class="space-y-4">
                                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                                                <div class="flex justify-between mb-2">
                                                    <div class="flex items-center gap-2">
                                                        <img class="w-8 h-8 rounded-full" src="https://ui-avatars.com/api/?name=Sarah+J." alt="Reviewer">
                                                        <div><p class="text-xs font-bold text-gray-900">Sarah Johnson</p><p class="text-[10px] text-gray-500">2 hours ago</p></div>
                                                    </div>
                                                    <div class="flex text-yellow-400">
                                                        <svg class="w-4 h-4 fill-current text-indigo-500" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                        <span class="ml-1 text-xs font-bold text-indigo-600">5.0</span>
                                                    </div>
                                                </div>
                                                <p class="text-sm text-gray-700 leading-relaxed italic">"The food was absolutely divine! Best pizza in town. Delivery was also surprisingly quick."</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900 uppercase mb-4">Pending Complaints</h4>
                                        <div class="space-y-3">
                                            <div class="p-4 border-l-4 border-red-500 bg-red-50 rounded-lg">
                                                <div class="flex justify-between mb-1"><span class="text-[10px] font-bold text-red-600 uppercase tracking-wider">Late Delivery</span><span class="text-[10px] text-gray-500">ORD-992</span></div>
                                                <p class="text-xs text-gray-900 line-clamp-2">"Customer reported 45 min delay. Driver was stuck in traffic."</p>
                                            </div>
                                            <div class="p-4 border-l-4 border-amber-500 bg-amber-50 rounded-lg">
                                                <div class="flex justify-between mb-1"><span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider">Missing Item</span><span class="text-[10px] text-gray-500">ORD-851</span></div>
                                                <p class="text-xs text-gray-900 line-clamp-2">"Missing side beverage mentioned. Resolved via partial refund."</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: Logistics -->
                            <div id="content-logistics" class="tab-content hidden-el transition-opacity duration-300">
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                                    <div class="lg:col-span-2">
                                        <div class="bg-gray-100 rounded-xl h-96 relative overflow-hidden border border-gray-200">
                                            <!-- Map Placeholder -->
                                            <div class="absolute inset-0 bg-[url('https://api.mapbox.com/styles/v1/mapbox/light-v10/static/pin-s+indigo(50.6,26.1),pin-s+red(50.61,26.11)/50.6,26.1,12/800x600?access_token=dummy')] bg-cover opacity-60"></div>
                                            <div class="absolute inset-0 flex items-center justify-center bg-gray-900/5 backdrop-blur-[1px]">
                                                <div class="bg-white/90 p-4 rounded-xl shadow-2xl text-center border-white border">
                                                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                    </div>
                                                    <h5 class="font-bold text-gray-900">Static Map Location</h5>
                                                    <p class="text-xs text-gray-500">Delivery Radius: 5.0 km</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="space-y-6">
                                        <h4 class="text-sm font-bold text-gray-900 uppercase">Recent Assigned Drivers</h4>
                                        <div class="space-y-3">
                                            <div class="flex items-center p-3 border border-gray-100 rounded-xl hover:bg-gray-50 transition">
                                                <img class="w-10 h-10 rounded-full mr-3" src="https://ui-avatars.com/api/?name=Ahmed+R.&background=random" alt="Driver">
                                                <div class="flex-1">
                                                    <p class="text-xs font-bold text-gray-900">Ahmed Rashid</p>
                                                    <p class="text-[10px] text-gray-500">Honda Civic - JED 4021</p>
                                                </div>
                                                <span class="text-[10px] font-bold text-indigo-600">Active</span>
                                            </div>
                                            <div class="flex items-center p-3 border border-gray-100 rounded-xl hover:bg-gray-50 transition">
                                                <img class="w-10 h-10 rounded-full mr-3" src="https://ui-avatars.com/api/?name=Ali+H.&background=random" alt="Driver">
                                                <div class="flex-1">
                                                    <p class="text-xs font-bold text-gray-900">Ali Hassan</p>
                                                    <p class="text-[10px] text-gray-500">Toyota Yaris - RIY 1192</p>
                                                </div>
                                                <span class="text-[10px] font-bold text-gray-400 font-mono">Idle</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: Documents -->
                            <div id="content-documents" class="tab-content hidden-el transition-opacity duration-300">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div class="relative group">
                                        <div class="aspect-video bg-gray-100 rounded-xl overflow-hidden border border-gray-200">
                                            <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=400&h=300&fit=crop" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                <button onclick="openImageModal(this.src)" class="bg-white/20 backdrop-blur-md text-white p-3 rounded-full hover:bg-white/40 transition">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mt-3 flex justify-between items-start">
                                            <div><p class="text-sm font-bold text-gray-900">Business License</p><p class="text-[10px] text-red-500 font-bold uppercase">Expires in 12 days</p></div>
                                            <button class="bg-gray-50 hover:bg-gray-100 p-2 rounded-lg transition border border-gray-100 shadow-sm"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg></button>
                                        </div>
                                    </div>
                                    <div class="relative group">
                                        <div class="aspect-video bg-gray-100 rounded-xl overflow-hidden border border-gray-200">
                                            <img src="https://images.unsplash.com/photo-1633158829585-23ba8f7c8caf?w=400&h=300&fit=crop" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                <button class="bg-white/20 backdrop-blur-md text-white p-3 rounded-full hover:bg-white/40 transition">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mt-3 flex justify-between items-start">
                                            <div><p class="text-sm font-bold text-gray-900">Health Certificate</p><p class="text-[10px] text-green-500 font-bold uppercase">Valid - Auto Renewal Enabled</p></div>
                                        </div>
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
    <!-- Notification Modal -->
    <div id="notificationModal" class="relative z-50 hidden-el" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg modal-content">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">Push Notification</h3>
                        <button onclick="closeModal('notificationModal')" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></button>
                    </div>
                    <div class="p-6">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Message Subject</label>
                        <input type="text" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm mb-4 focus:ring-1 focus:ring-primary focus:border-primary outline-none" placeholder="e.g. Schedule Maintenance Notice">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Detailed Content</label>
                        <textarea rows="4" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none" placeholder="Enter notification message here..."></textarea>
                        <div class="mt-6 flex gap-3">
                            <button onclick="handleAction('Notification Sent')" class="flex-1 bg-primary text-white py-2 rounded-lg font-bold text-sm hover:bg-primary_dark transition">Send Now</button>
                            <button onclick="closeModal('notificationModal')" class="flex-1 bg-gray-100 text-gray-600 py-2 rounded-lg font-bold text-sm hover:bg-gray-200 transition">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Commission Modal -->
    <div id="commissionModal" class="relative z-50 hidden-el" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md modal-content">
                    <div class="p-6 text-center">
                        <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4"><svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Adjust Commission</h3>
                        <p class="text-sm text-gray-500 mb-6">Set a custom commission percentage for this restaurant.</p>
                        <div class="relative max-w-[120px] mx-auto">
                            <input type="number" value="12" class="w-full border-2 border-primary rounded-xl p-3 text-center text-2xl font-bold bg-indigo-50 outline-none text-indigo-700">
                            <span class="absolute right-4 top-4 text-indigo-400 font-bold">%</span>
                        </div>
                        <div class="mt-8 flex gap-3">
                            <button onclick="handleAction('Commission Updated')" class="flex-1 bg-primary text-white py-2.5 rounded-lg font-bold text-sm shadow-md">Apply Change</button>
                            <button onclick="closeModal('commissionModal')" class="flex-1 bg-white border border-gray-200 text-gray-600 py-2.5 rounded-lg font-bold text-sm">Discard</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="relative z-[100] hidden-el">
        <div class="fixed inset-0 bg-black/90 backdrop-blur-sm modal-overlay" onclick="closeModal('imageModal')"></div>
        <div class="fixed inset-0 z-10 flex items-center justify-center p-4" onclick="closeModal('imageModal')">
            <img id="enlargedImg" src="" class="max-w-[90vw] max-h-[90vh] object-contain rounded shadow-2xl modal-content">
        </div>
    </div>

    <div id="toast-container"></div>

    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    <script src="{{ asset('modules/admin/js/restaurant-details.js') }}"></script>
</body>
</html>
