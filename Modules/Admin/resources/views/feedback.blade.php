<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تقييمات وآراء المستخدمين - لوحة الإدارة</title>
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
        .star-filled { color: #f59e0b; }
        .star-empty { color: #e5e7eb; }
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
            
            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">تقييمات وآراء المستخدمين المباشرة</h2>
                    <p class="text-sm text-gray-500 mt-1">متابعة وتحليل تقييمات العملاء للسائقين والمطاعم.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Search Input -->
                    <div class="relative min-w-[220px]">
                        <input type="text" id="fbSearch" placeholder="البحث بالعميل، الجهة، أو الملاحظة..." class="w-full rounded-lg border-gray-300 py-2 pr-9 pl-3 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary shadow-sm bg-white text-right">
                        <svg class="w-4 h-4 text-gray-400 absolute right-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>

                    <!-- Rating Score Filter -->
                    <select id="fbScoreFilter" class="rounded-lg border-gray-300 py-2 pr-3 pl-8 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary shadow-sm bg-white">
                        <option value="">جميع التقييمات</option>
                        <option value="5">5 نجوم</option>
                        <option value="4">4 نجوم</option>
                        <option value="3">3 نجوم</option>
                        <option value="2">2 نجوم</option>
                        <option value="1">نجمة واحدة</option>
                    </select>

                    <!-- Entity Type Filter -->
                    <select id="fbFilter" class="rounded-lg border-gray-300 py-2 pr-3 pl-8 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary shadow-sm bg-white">
                        <option value="ALL">جميع الجهات</option>
                        <option value="RESTAURANT">تقييمات المطاعم</option>
                        <option value="DRIVER">تقييمات السائقين</option>
                    </select>
                </div>
            </div>

            <!-- Dashboard Split Layout for Stats -->
            <div class="flex flex-col lg:flex-row gap-6 mb-8">
                
                <!-- Average Rating Card -->
                <div class="w-full lg:w-1/3">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col items-center justify-center h-full text-center">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">معدل الرضا العام</p>
                        <h3 id="globalSatValue" class="text-6xl font-extrabold text-gray-900 mb-2">5.0</h3>
                        <div id="globalSatStars" class="flex text-warning gap-1 mb-2 justify-center">
                            <!-- Star Icons rendered dynamically -->
                        </div>
                        <p class="text-sm text-gray-500">بناءً على <span id="totalReviewsCount" class="font-bold text-gray-900">0</span> مراجعة إجمالية.</p>
                    </div>
                </div>

                <!-- Star Distribution Progress Bars -->
                <div class="w-full lg:w-2/3">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 text-right">توزيع التقييمات</h4>
                        <div class="space-y-3">
                            <!-- Bar 5 -->
                            <div class="flex items-center text-sm">
                                <span class="w-16 text-gray-600 font-medium whitespace-nowrap text-right me-2">5 نجوم</span>
                                <div class="w-full bg-gray-100 rounded-sm h-3 relative overflow-hidden">
                                    <div id="dist5Bar" class="bg-warning h-3 rounded-sm transition-all duration-500" style="width: 0%"></div>
                                </div>
                                <span id="dist5Pct" class="w-12 text-left text-gray-500 font-medium ms-2 dir-ltr">0%</span>
                            </div>
                            <!-- Bar 4 -->
                            <div class="flex items-center text-sm">
                                <span class="w-16 text-gray-600 font-medium whitespace-nowrap text-right me-2">4 نجوم</span>
                                <div class="w-full bg-gray-100 rounded-sm h-3 relative overflow-hidden">
                                    <div id="dist4Bar" class="bg-warning h-3 rounded-sm opacity-90 transition-all duration-500" style="width: 0%"></div>
                                </div>
                                <span id="dist4Pct" class="w-12 text-left text-gray-500 font-medium ms-2 dir-ltr">0%</span>
                            </div>
                            <!-- Bar 3 -->
                            <div class="flex items-center text-sm">
                                <span class="w-16 text-gray-600 font-medium whitespace-nowrap text-right me-2">3 نجوم</span>
                                <div class="w-full bg-gray-100 rounded-sm h-3 relative overflow-hidden">
                                    <div id="dist3Bar" class="bg-warning h-3 rounded-sm opacity-70 transition-all duration-500" style="width: 0%"></div>
                                </div>
                                <span id="dist3Pct" class="w-12 text-left text-gray-500 font-medium ms-2 dir-ltr">0%</span>
                            </div>
                            <!-- Bar 2 -->
                            <div class="flex items-center text-sm">
                                <span class="w-16 text-gray-600 font-medium whitespace-nowrap text-right me-2">2 نجوم</span>
                                <div class="w-full bg-gray-100 rounded-sm h-3 relative overflow-hidden">
                                    <div id="dist2Bar" class="bg-warning h-3 rounded-sm opacity-50 transition-all duration-500" style="width: 0%"></div>
                                </div>
                                <span id="dist2Pct" class="w-12 text-left text-gray-500 font-medium ms-2 dir-ltr">0%</span>
                            </div>
                            <!-- Bar 1 -->
                            <div class="flex items-center text-sm">
                                <span class="w-16 text-gray-600 font-medium whitespace-nowrap text-right me-2">نجمة واحدة</span>
                                <div class="w-full bg-gray-100 rounded-sm h-3 relative overflow-hidden">
                                    <div id="dist1Bar" class="bg-warning h-3 rounded-sm opacity-30 transition-all duration-500" style="width: 0%"></div>
                                </div>
                                <span id="dist1Pct" class="w-12 text-left text-gray-500 font-medium ms-2 dir-ltr">0%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto w-full">
                    <table class="w-full whitespace-nowrap text-right text-sm text-gray-500">
                        <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200 text-right">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-right">المستخدم / العميل</th>
                                <th scope="col" class="px-6 py-3 text-right">نوع الجهة</th>
                                <th scope="col" class="px-6 py-3 text-right">التقييم</th>
                                <th scope="col" class="px-6 py-3 text-right">نص الملاحظة</th>
                                <th scope="col" class="px-6 py-3 text-left">التاريخ</th>
                            </tr>
                        </thead>
                        <tbody id="fbTableBody" class="divide-y divide-gray-200 bg-white">
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="hidden-el p-12 flex flex-col items-center justify-center text-center border-t border-gray-200">
                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    <h3 class="text-lg font-medium text-gray-900">لم يتم العثور على تقييمات</h3>
                    <p class="text-sm text-gray-500 mt-1">لا توجد تقييمات مطابقة لمعايير البحث المحددة.</p>
                </div>

                <!-- Pagination Footer -->
                <div id="paginationWrapper" class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p id="paginationInfo" class="text-xs text-gray-500">عرض الصفحة 1 من 1</p>
                    <div id="paginationNav" class="flex items-center space-x-1 space-x-reverse">
                        <!-- Rendered by JS -->
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    <script src="{{ asset('modules/admin/js/feedback.js') }}"></script>
</body>
</html>
