<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles & Permissions - Admin Dashboard</title>
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
        html[dir="rtl"] body { font-family: 'Cairo', sans-serif !important; }
        html[dir="rtl"] .ml-3 { margin-left: 0 !important; margin-right: 0.75rem !important; }
        html[dir="rtl"] .ml-4 { margin-left: 0 !important; margin-right: 1rem !important; }
        html[dir="rtl"] aside { left: auto !important; right: 0 !important; border-right: none !important; border-left: 1px solid #e5e7eb !important; }
        html[dir="rtl"] .space-x-4 > :not([hidden]) ~ :not([hidden]) { --tw-space-x-reverse: 1 !important; }
        html[dir="rtl"] .space-x-2 > :not([hidden]) ~ :not([hidden]) { --tw-space-x-reverse: 1 !important; }
        html[dir="rtl"] #langDropdownMenu, html[dir="rtl"] #profileDropdownMenu { right: auto !important; left: 0 !important; }
    </style></head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased overflow-hidden flex h-screen">

    @include('admin::layouts.partials.sidebar')







    <!-- Layout Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300">

        @include('admin::layouts.partials.header')

        <!-- Main Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto w-full">

            <!-- Toast Alert -->
            <div id="toast"
                class="hidden-el fixed bottom-4 right-4 z-50 rounded-md bg-green-50 p-4 shadow-lg border border-green-200 transition-opacity duration-300">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400 border-green-200" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800" id="toastMessage">Success</p>
                    </div>
                </div>
            </div>

            <div
                class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Roles & Permissions</h2>
                    <p class="text-sm text-gray-500 mt-1">Configure role-based access control (RBAC) across system
                        modules.</p>
                </div>
                <div class="flex space-x-2">
                    <button onclick="openRoleModal()"
                        class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary_dark focus:outline-none transition-colors whitespace-nowrap">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        Create Role
                    </button>
                    <button onclick="savePermissions()"
                        class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition-colors whitespace-nowrap">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Save Policy
                    </button>
                </div>
            </div>

            <!-- Dashboard Split Layout -->
            <div class="flex flex-col lg:flex-row gap-6">

                <!-- Roles Column -->
                <div class="w-full lg:w-1/4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-4 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider">System Roles</h3>
                        </div>
                        <ul class="divide-y divide-gray-100 max-h-[600px] overflow-y-auto" id="rolesList">
                            <!-- Populated by JS -->
                        </ul>
                    </div>
                </div>

                <!-- Permissions Matrix Column -->
                <div class="w-full lg:w-3/4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900" id="matrixTitle">Permissions for Super Admin
                                </h3>
                                <p class="text-xs text-gray-500 mt-1" id="matrixSubtitle">Full system access enabled.
                                </p>
                            </div>
                        </div>

                        <div class="p-6">
                            <!-- The Grid headers -->
                            <div class="hidden sm:grid grid-cols-7 gap-2 mb-4 pb-2 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider items-center">
                                <div class="col-span-1 flex items-center">
                                    <span class="mr-2">Module</span>
                                </div>
                                <div class="text-center flex flex-col items-center">
                                    <label class="cursor-pointer mb-1 hover:text-primary">View</label>
                                    <input type="checkbox" id="selectAll_view" onchange="toggleSelectAllColumn('view', this.checked)" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                                </div>
                                <div class="text-center flex flex-col items-center">
                                    <label class="cursor-pointer mb-1 hover:text-primary">Create</label>
                                    <input type="checkbox" id="selectAll_create" onchange="toggleSelectAllColumn('create', this.checked)" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                                </div>
                                <div class="text-center flex flex-col items-center">
                                    <label class="cursor-pointer mb-1 hover:text-primary">Edit</label>
                                    <input type="checkbox" id="selectAll_edit" onchange="toggleSelectAllColumn('edit', this.checked)" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                                </div>
                                <div class="text-center flex flex-col items-center">
                                    <label class="cursor-pointer mb-1 hover:text-primary">Delete</label>
                                    <input type="checkbox" id="selectAll_delete" onchange="toggleSelectAllColumn('delete', this.checked)" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                                </div>
                                <div class="text-center flex flex-col items-center">
                                    <label class="cursor-pointer mb-1 hover:text-primary">Manage</label>
                                    <input type="checkbox" id="selectAll_manage" onchange="toggleSelectAllColumn('manage', this.checked)" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                                </div>
                                <div class="text-center flex flex-col items-center">
                                    <label class="cursor-pointer mb-1 hover:text-primary">Respond</label>
                                    <input type="checkbox" id="selectAll_respond" onchange="toggleSelectAllColumn('respond', this.checked)" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                                </div>
                            </div>

                            <!-- Populated by JS -->
                            <div id="permissionsContainer" class="space-y-4 sm:space-y-0">

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Create/Edit Role Modal -->
    <div id="roleModal" class="relative z-40 hidden-el" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity modal-overlay"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md modal-content">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900" id="roleModalTitle">Create Global Role</h3>
                        <button onclick="closeModal('roleModal')"
                            class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form id="roleForm" onsubmit="handleRoleSubmit(event)">
                        <input type="hidden" id="roleId">
                        <div class="px-6 py-5 space-y-4">

                            <div>
                                <label for="roleNameInput" class="block text-sm font-medium text-gray-700">Role
                                    Name</label>
                                <input type="text" id="roleNameInput" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-10 px-3 border"
                                    placeholder="e.g. Finance Manager">
                            </div>

                            <div>
                                <label for="roleDescInput"
                                    class="block text-sm font-medium text-gray-700">Description</label>
                                <input type="text" id="roleDescInput"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm h-10 px-3 border"
                                    placeholder="e.g. Access to payment modules...">
                            </div>

                        </div>
                        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                            <button type="button" onclick="closeModal('roleModal')"
                                class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 focus:outline-none">Cancel</button>
                            <button type="submit"
                                class="bg-primary border border-transparent text-white px-4 py-2 rounded-md shadow-sm text-sm font-medium hover:bg-primary_dark focus:outline-none">Save
                                Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    <script src="{{ asset('modules/admin/js/roles-permissions.js') }}"></script>
</body>

</html>