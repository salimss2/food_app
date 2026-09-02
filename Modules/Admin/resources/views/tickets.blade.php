<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>مركز التذاكر والدعم الفني - لوحة الإدارة</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4f46e5',
                        primary_dark: '#4338ca',
                        warning: '#f59e0b',
                    },
                    fontFamily: {
                        sans: ['Cairo', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('modules/admin/css/app.css') }}">
    <style>
        body { font-family: 'Cairo', sans-serif !important; }
        .hidden-el { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased overflow-hidden flex h-screen" dir="rtl">

    @include('admin::layouts.partials.sidebar')

    <!-- Layout Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300">
        
        @include('admin::layouts.partials.header')

        <!-- Main Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto w-full text-right">
            
            <!-- Page Title & Header -->
            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">مركز التذاكر والدعم الفني</h2>
                    <p class="text-sm text-gray-500 mt-1">إدارة ومتابعة بلاغات واستفسارات العملاء والرد عليها بشكل فوري.</p>
                </div>
            </div>

            <!-- Summary KPI Metric Cards (4 Cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <!-- Card 1: Total Tickets -->
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">إجمالي التذاكر</p>
                        <h3 id="totalTicketsMetric" class="text-3xl font-extrabold text-gray-900 mt-1">0</h3>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                </div>

                <!-- Card 2: Pending Complaints -->
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">الشكاوى المعلقة</p>
                        <h3 id="pendingComplaintsMetric" class="text-3xl font-extrabold text-red-600 mt-1">0</h3>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center font-bold text-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>

                <!-- Card 3: Active Inquiries -->
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">الاستفسارات النشطة</p>
                        <h3 id="openInquiriesMetric" class="text-3xl font-extrabold text-blue-600 mt-1">0</h3>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>

                <!-- Card 4: Avg Response Time -->
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">معدل وقت الاستجابة</p>
                        <h3 id="avgResponseTimeMetric" class="text-2xl font-extrabold text-emerald-600 mt-1">0 دقيقة</h3>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Bar -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <!-- Search Input -->
                <div class="relative flex-1 min-w-[240px]">
                    <input type="text" id="ticketSearch" placeholder="البحث برقم التذكرة، اسم العميل، رقم الهاتف، أو الموضوع..." class="w-full rounded-lg border-gray-300 py-2.5 pr-10 pl-4 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary shadow-sm bg-white text-right">
                    <svg class="w-5 h-5 text-gray-400 absolute right-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <!-- Type Toggle -->
                    <select id="typeFilter" class="rounded-lg border-gray-300 py-2.5 pr-3 pl-8 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary shadow-sm bg-white font-medium">
                        <option value="all">جميع التذاكر</option>
                        <option value="complaint">الشكاوى فقط</option>
                        <option value="inquiry">الاستفسارات فقط</option>
                    </select>

                    <!-- Status Filter -->
                    <select id="statusFilter" class="rounded-lg border-gray-300 py-2.5 pr-3 pl-8 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary shadow-sm bg-white font-medium">
                        <option value="all">جميع الحالات</option>
                        <option value="pending">قيد الانتظار</option>
                        <option value="in_progress">قيد المعالجة</option>
                        <option value="resolved">تم الحل / الرد</option>
                        <option value="rejected">مرفوضة</option>
                        <option value="closed">مغلقة</option>
                    </select>
                </div>
            </div>

            <!-- Tickets Data Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto w-full">
                    <table class="w-full whitespace-nowrap text-right text-sm text-gray-500">
                        <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-right">رقم التذكرة</th>
                                <th scope="col" class="px-6 py-3.5 text-right">العميل / المستخدم</th>
                                <th scope="col" class="px-6 py-3.5 text-right">النوع</th>
                                <th scope="col" class="px-6 py-3.5 text-right">الموضوع</th>
                                <th scope="col" class="px-6 py-3.5 text-right">التاريخ</th>
                                <th scope="col" class="px-6 py-3.5 text-right">الحالة</th>
                                <th scope="col" class="px-6 py-3.5 text-left">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody id="ticketsTableBody" class="divide-y divide-gray-200 bg-white">
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div id="ticketsEmptyState" class="hidden-el p-12 flex flex-col items-center justify-center text-center border-t border-gray-200">
                    <svg class="w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    <h3 class="text-lg font-bold text-gray-900">لم يتم العثور على تذاكر دعم</h3>
                    <p class="text-sm text-gray-500 mt-1">لا توجد تذاكر دعم أو بلاغات مطابقة لمعايير البحث الحالية.</p>
                </div>

                <!-- Pagination Footer -->
                <div id="ticketsPaginationWrapper" class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p id="ticketsPaginationInfo" class="text-xs text-gray-500 font-medium">عرض الصفحة 1 من 1</p>
                    <div id="ticketsPaginationNav" class="flex items-center space-x-1 space-x-reverse">
                        <!-- Rendered by JS -->
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Interactive Ticket Review & Response Modal (2-Pane Layout) -->
    <div id="ticketModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden-el p-4 sm:p-6 transition-all duration-300">
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-5xl max-h-[92vh] flex flex-col overflow-hidden text-right transform transition-all">
            
            <!-- Modal Header (Clean Light Theme) -->
            <div class="px-6 py-4 bg-white/90 backdrop-blur-md flex items-center justify-between border-b border-slate-100/90 sticky top-0 z-10">
                <!-- Right Side (RTL): Metadata Group -->
                <div class="flex items-center gap-3">
                    <span id="modalTicketCode" class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200/70 shadow-2xs tracking-wider">
                        #CP-1052
                    </span>
                    <span id="modalTicketTypeBadge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200/80 shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                        <span>شكوى</span>
                    </span>
                </div>

                <!-- Left Side (RTL): Modern Close Button -->
                <button type="button" onclick="closeTicketModal()" class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-slate-700 bg-slate-100/80 hover:bg-slate-200/80 rounded-full transition-all duration-200 shadow-2xs focus:outline-none focus:ring-2 focus:ring-slate-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Modal Body (2-Pane Split Grid) -->
            <div class="flex-1 overflow-y-auto p-6 sm:p-8 grid grid-cols-1 lg:grid-cols-12 gap-6 bg-slate-50/60">
                
                <!-- Right Pane (RTL): Customer Info & Original Ticket Context -->
                <div class="lg:col-span-5 bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm flex flex-col justify-between space-y-6">
                    <div class="space-y-6">
                        <!-- Customer Info Card -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">بيانات العميل صاحب التذكرة</h4>
                            </div>
                            <div class="flex items-center p-3.5 bg-slate-50/80 rounded-2xl border border-slate-100">
                                <div id="modalCustomerAvatar" class="w-12 h-12 rounded-full bg-gradient-to-tr from-indigo-600 to-indigo-500 text-white font-extrabold text-sm flex items-center justify-center ml-3.5 shadow-sm ring-2 ring-indigo-100 overflow-hidden">
                                    ع
                                </div>
                                <div class="space-y-0.5">
                                    <h5 id="modalCustomerName" class="font-bold text-slate-900 text-base">اسم العميل</h5>
                                    <p id="modalCustomerPhone" class="text-xs font-semibold text-slate-500 font-mono" dir="ltr">+967 770 000 000</p>
                                </div>
                            </div>
                        </div>

                        <!-- Ticket Metadata Grid -->
                        <div class="grid grid-cols-2 gap-3 p-4 bg-slate-50/70 rounded-2xl border border-slate-100 text-xs">
                            <div class="space-y-1">
                                <span class="text-slate-400 font-medium block">الفئة / التصنيف</span>
                                <span id="modalCategory" class="inline-block px-2.5 py-1 rounded-lg bg-white border border-slate-200/80 font-bold text-slate-800 shadow-2xs">عام</span>
                            </div>
                            <div class="space-y-1">
                                <span class="text-slate-400 font-medium block">المرجع المرتبط</span>
                                <span id="modalRelatedId" class="inline-block px-2.5 py-1 rounded-lg bg-white border border-indigo-100 font-bold text-indigo-600 shadow-2xs">#ORD-5012</span>
                            </div>
                            <div class="col-span-2 space-y-1 pt-2 border-t border-slate-200/60">
                                <span class="text-slate-400 font-medium block">تاريخ ووقت الإرسال</span>
                                <span id="modalCreatedAt" class="font-semibold text-slate-700">اليوم، 10:00 ص</span>
                            </div>
                        </div>

                        <!-- Original User Message Callout Box -->
                        <div class="space-y-2.5">
                            <div class="flex items-center gap-2 text-indigo-900">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">نص الرسالة الأصلي</h4>
                            </div>
                            <div class="p-4.5 bg-indigo-50/40 rounded-2xl border border-indigo-100/80 text-slate-800 text-sm leading-relaxed font-medium">
                                <h6 id="modalSubject" class="font-bold text-slate-900 text-base mb-2 border-b border-indigo-100/90 pb-2">موضوع التذكرة</h6>
                                <p id="modalUserMessage" class="whitespace-pre-line text-slate-700 text-sm leading-relaxed">محتوى رسالة العميل بالتفصيل...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Left Pane (RTL): Admin Response Action & Activity Bubble -->
                <div class="lg:col-span-7 bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm flex flex-col justify-between space-y-6">
                    <form id="ticketResponseForm" onsubmit="submitTicketResponse(event)" class="space-y-5 flex-1 flex flex-col justify-between">
                        <input type="hidden" id="modalTicketId" value="">

                        <div class="space-y-5">
                            <!-- Status Selector -->
                            <div class="space-y-1.5">
                                <label for="modalStatusSelect" class="block text-xs font-bold text-slate-700">تحديث حالة التذكرة إلى:</label>
                                <div class="relative">
                                    <select id="modalStatusSelect" class="w-full rounded-xl border-slate-200 py-3 pr-4 pl-10 text-sm font-bold text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 shadow-2xs bg-slate-50/40 hover:bg-white transition-colors cursor-pointer appearance-none">
                                        <option value="pending">🟡 قيد الانتظار (Pending)</option>
                                        <option value="in_progress">🔵 قيد المعالجة (In Progress)</option>
                                        <option value="resolved">🟢 تم الحل / الرد (Resolved)</option>
                                        <option value="rejected">🔴 مرفوضة (Rejected)</option>
                                        <option value="closed">⚪ مغلقة (Closed)</option>
                                    </select>
                                    <div class="pointer-events-none absolute left-3.5 top-3.5 text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Admin Response Textarea -->
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <label for="modalAdminResponse" class="block text-xs font-bold text-slate-700">نص رد الإدارة / التوجيه للعميل:</label>
                                    <span class="text-xs text-slate-400 font-medium">سيتم إرسال إشعار فوري للعميل</span>
                                </div>
                                <textarea id="modalAdminResponse" rows="6" placeholder="اكتب تفاصيل الرد المباشر أو الإجراء المتخذ لحل التذكرة..." class="w-full rounded-2xl border-slate-200 p-4 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 shadow-2xs bg-slate-50/30 hover:bg-white focus:bg-white transition-all font-medium text-slate-800 leading-relaxed placeholder:text-slate-400" required></textarea>
                            </div>

                            <!-- Response History Activity Bubble -->
                            <div id="modalHistoryBox" class="hidden-el p-4 bg-slate-50 rounded-2xl border border-slate-200/80 text-xs space-y-1.5">
                                <div class="flex items-center justify-between text-slate-600">
                                    <span class="font-bold text-slate-800 flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-2xs"></span>
                                        آخر رد من الإدارة بواسطة: <span id="modalAdminName" class="text-indigo-600 font-extrabold">Admin</span>
                                    </span>
                                    <span id="modalRespondedAt" class="text-slate-400 font-semibold font-mono">تاريخ الرد</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons (Modal Footer Alignment) -->
                        <div class="flex items-center justify-start gap-3 pt-4 border-t border-slate-100">
                            <button type="submit" id="btnSubmitResponse" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-sm transition-all duration-200 flex items-center justify-center gap-2 active:scale-98">
                                <span>تحديث التذكرة وإرسال الرد</span>
                            </button>
                            <button type="button" onclick="closeTicketModal()" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-sm font-semibold transition-colors">إلغاء</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    <script src="{{ asset('modules/admin/js/tickets.js') }}"></script>
</body>
</html>
