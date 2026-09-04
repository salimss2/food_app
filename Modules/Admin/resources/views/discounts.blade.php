<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>إدارة أكواد الخصم والكوبونات - لوحة التحكم</title>
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
                        sans: ['Cairo', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="{{ asset('modules/admin/css/app.css') }}">
    <!-- Cairo Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif !important; }
        .hidden-el { display: none !important; }
        html[dir="rtl"] aside { left: auto !important; right: 0 !important; border-right: none !important; border-left: 1px solid #e5e7eb !important; }
        html[dir="rtl"] #langDropdownMenu, html[dir="rtl"] #profileDropdownMenu { right: auto !important; left: 0 !important; }
    </style>
</head>
<body class="bg-slate-50/70 text-slate-800 font-sans antialiased overflow-hidden flex h-screen">

    @include('admin::layouts.partials.sidebar')

    <!-- Layout Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300">
        
        @include('admin::layouts.partials.header')

        <!-- Main Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto w-full">
            
            <!-- Toast Alert -->
            <div id="toast" class="hidden-el fixed bottom-5 left-5 z-50 rounded-2xl bg-emerald-600 text-white p-4 shadow-xl border border-emerald-500 transition-all duration-300 flex items-center gap-3">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-extrabold" id="toastMessage">تم العملية بنجاح</p>
                </div>
            </div>

            <!-- Page Header -->
            <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2.5">
                        <span>إدارة كودات وأكواد الخصم (Coupons)</span>
                        <span class="px-3 py-1 rounded-full text-xs font-black bg-indigo-100 text-indigo-700 border border-indigo-200">
                            {{ is_countable($discountCodes ?? null) ? count($discountCodes) : 0 }} كود
                        </span>
                    </h1>
                    <p class="text-xs text-slate-500 mt-1 font-semibold">إنشاء وإدارة أكواد الحسومات والكوبونات المخصصة للزبائن والمطاعم</p>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <select id="discountFilterType" class="rounded-xl border-slate-200 py-2.5 px-3.5 text-xs font-bold focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-2xs bg-white text-slate-700" onchange="filterDiscountsTable()">
                        <option value="ALL">جميع أنواع الخصم</option>
                        <option value="percentage">نسبة مئوية (%)</option>
                        <option value="fixed">مبلغ ثابت (YER)</option>
                    </select>
                    <button onclick="openCreateDiscountModal()" class="inline-flex items-center justify-center px-4 py-2.5 border border-transparent rounded-xl shadow-sm text-xs font-extrabold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 transition-all whitespace-nowrap gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        <span>إنشاء كود خصم جديد +</span>
                    </button>
                </div>
            </div>

            <!-- KPI Metric Cards Grid (3 Cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
                <!-- Active Codes -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-extrabold text-slate-500">الأكواد النشطة الحالية</span>
                        </div>
                        <div class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($activeCodesCount ?? ($discountCodes ?? collect())->where('is_active', true)->count()) }}</div>
                        <div class="text-3xs text-emerald-600 font-bold mt-1">جاهزة للاستخدام في التطبيق</div>
                    </div>
                    <div class="w-13 h-13 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>

                <!-- Total Redemptions -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-extrabold text-slate-500 block mb-1">إجمالي مرات الاستخدام</span>
                        <div class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($totalRedemptions ?? ($discountCodes ?? collect())->sum('used_count')) }}</div>
                        <div class="text-3xs text-indigo-600 font-bold mt-1">إجمالي عمليات الفدية والتخفيض</div>
                    </div>
                    <div class="w-13 h-13 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                </div>

                <!-- Total Codes -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-extrabold text-slate-500 block mb-1">إجمالي الأكواد المتاحة</span>
                        <div class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($totalCodesCount ?? count($discountCodes ?? [])) }}</div>
                        <div class="text-3xs text-slate-500 font-bold mt-1">يشمل الكودات المنتهية والنشطة</div>
                    </div>
                    <div class="w-13 h-13 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 001.105 1.79l.01.005A2 2 0 014 15.79l-.01.005A2 2 0 002 17.58V20a2 2 0 002 2h16a2 2 0 002-2v-2.42a2 2 0 00-1.99-1.995l-.01-.005A2 2 0 0120 13.79l.01-.005A2 2 0 0022 12V7a2 2 0 00-2-2H5z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Coupons Data Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-slate-50/50">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                            <span>قائمة أكواد الخصم النشطة والمنتهية</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-indigo-100 text-indigo-700 border border-indigo-200">{{ is_countable($discountCodes ?? null) ? count($discountCodes) : 0 }}</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">تفاصيل شروط التخفيض، صلاحيات الاستخدام، ومعدل الاستهلاك</p>
                    </div>
                </div>

                <div class="overflow-x-auto w-full">
                    <table class="w-full text-right text-sm text-slate-600">
                        <thead class="bg-slate-50 text-slate-700 font-bold text-xs uppercase border-b border-slate-200/80">
                            <tr>
                                <th scope="col" class="px-6 py-4">كود الخصم</th>
                                <th scope="col" class="px-6 py-4">قيمة ونوع الخصم</th>
                                <th scope="col" class="px-6 py-4">الشروط والقيود والنطاق</th>
                                <th scope="col" class="px-6 py-4">معدل الاستهلاك</th>
                                <th scope="col" class="px-6 py-4">تاريخ الانتهاء</th>
                                <th scope="col" class="px-6 py-4">حالة التفعيل</th>
                                <th scope="col" class="px-6 py-4 text-left">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody id="discountsTableBody" class="divide-y divide-slate-100 bg-white">
                            @forelse($discountCodes ?? [] as $code)
                                @php
                                    $isExpired = \Carbon\Carbon::parse($code->expiry_date)->isPast();
                                    $usagePercent = $code->max_usages > 0 ? min(round(($code->used_count / $code->max_usages) * 100), 100) : 0;
                                @endphp
                                <tr class="discount-row hover:bg-slate-50/70 transition-colors" data-type="{{ $code->discount_type }}" data-status="{{ $code->is_active && !$isExpired ? 'active' : 'inactive' }}">
                                    <!-- Code Badge + One Click Copy -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="px-3 py-1.5 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-800 font-black text-xs tracking-wider uppercase font-mono shadow-2xs">
                                                {{ $code->code }}
                                            </span>
                                            <button type="button" onclick="copyDiscountCode('{{ $code->code }}')" title="نسخ الكود" class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                            </button>
                                        </div>
                                    </td>

                                    <!-- Value & Type Badge -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col gap-1">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200 w-fit">
                                                @if($code->discount_type === 'percentage')
                                                    <span>خصم {{ number_format($code->discount_value, 0) }}%</span>
                                                @else
                                                    <span>خصم {{ number_format($code->discount_value) }} YER</span>
                                                @endif
                                            </span>
                                            @if($code->discount_type === 'percentage' && $code->max_discount_amount)
                                                <span class="text-3xs font-bold text-slate-500">(سقف الخصم: {{ number_format($code->max_discount_amount) }} YER)</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Conditions & Scope -->
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap items-center gap-1.5 max-w-xs">
                                            @if($code->min_order_amount > 0)
                                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-3xs font-bold border border-slate-200">
                                                    حد أدنى: {{ number_format($code->min_order_amount) }} YER
                                                </span>
                                            @endif
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-3xs font-bold border border-slate-200">
                                                لكل مستخدم: {{ $code->per_user_limit ?? 1 }}
                                            </span>
                                            @if($code->restaurant)
                                                <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 text-3xs font-bold border border-indigo-200">
                                                    🏪 {{ $code->restaurant->name }}
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-3xs font-bold border border-slate-200">
                                                    🌐 جميع المطاعم
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Usage Rate -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="w-36">
                                            <div class="flex justify-between items-center text-xs font-bold text-slate-700 mb-1">
                                                <span>{{ number_format($code->used_count) }}</span>
                                                <span class="text-slate-400">/ {{ number_format($code->max_usages) }}</span>
                                            </div>
                                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200/60">
                                                <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: {{ $usagePercent }}%"></div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Expiry Date -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($isExpired)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-3xs font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                                <span>🔴 منتهي:</span>
                                                <span>{{ \Carbon\Carbon::parse($code->expiry_date)->format('Y-m-d') }}</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-3xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span>🟢 ساري المفعول:</span>
                                                <span>{{ \Carbon\Carbon::parse($code->expiry_date)->format('Y-m-d') }}</span>
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Live Status Toggle Switch -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" value="1" {{ $code->is_active ? 'checked' : '' }} onchange="toggleDiscountStatus({{ $code->id }}, this)" class="sr-only peer">
                                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                        </label>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" onclick='editDiscount(@json($code))' class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-xl transition-colors cursor-pointer" title="تعديل">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            <button type="button" onclick="deleteDiscount({{ $code->id }})" class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl transition-colors cursor-pointer" title="حذف">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="w-16 h-16 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center mx-auto mb-3 text-slate-400">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 001.105 1.79l.01.005A2 2 0 014 15.79l-.01.005A2 2 0 002 17.58V20a2 2 0 002 2h16a2 2 0 002-2v-2.42a2 2 0 00-1.99-1.995l-.01-.005A2 2 0 0120 13.79l.01-.005A2 2 0 0022 12V7a2 2 0 00-2-2H5z"></path></svg>
                                        </div>
                                        <h3 class="text-sm font-extrabold text-slate-800">لا يوجد كودات خصم مسجلة حالياً</h3>
                                        <p class="text-xs text-slate-500 mt-1">اضغط على زر "إنشاء كود خصم جديد +" لبدء العروض</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(isset($discountCodes) && $discountCodes->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 bg-white">
                        {{ $discountCodes->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- Create/Edit Discount Modal (#discountModal) -->
    <div id="discountModal" class="relative z-50 hidden-el" aria-labelledby="discountModalTitle" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-3xl bg-white text-right shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-slate-100">
                    
                    <!-- Header -->
                    <div class="bg-slate-50/80 px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-base font-black text-slate-900 flex items-center gap-2" id="discountModalTitle">
                            <span>إنشاء كود خصم جديد</span>
                        </h3>
                        <button type="button" onclick="closeDiscountModal()" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors cursor-pointer">
                            <span class="sr-only">إغلاق</span>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    
                    <form id="discountForm" action="{{ route('admin.discounts.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="discountFormMethod" value="POST">
                        
                        <div class="px-6 py-5 space-y-4">
                            
                            <!-- Basic details -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="codeTitleInput" class="block text-xs font-extrabold text-slate-700 mb-1">كود الخصم (أحرف إنجليزية كبيرة)</label>
                                    <div class="relative">
                                        <input type="text" name="code" id="codeTitleInput" required style="text-transform:uppercase" class="w-full uppercase font-mono font-bold tracking-wider rounded-xl border-slate-200 text-xs h-11 px-3.5 pl-28 border bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="مثال: MUKALLA20">
                                        <button type="button" onclick="generateRandomPromoCode()" class="absolute left-1.5 top-1.5 bottom-1.5 px-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-3xs font-extrabold rounded-lg transition-all flex items-center gap-1 border border-indigo-200 cursor-pointer shadow-2xs" title="توليد كود تلقائي عشوائي">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                            <span>توليد تلقائي</span>
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <label for="expiryInput" class="block text-xs font-extrabold text-slate-700 mb-1">تاريخ الانتهاء</label>
                                    <input type="date" name="expiry_date" id="expiryInput" required class="w-full rounded-xl border-slate-200 text-xs font-bold h-11 px-3.5 border bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="typeInput" class="block text-xs font-extrabold text-slate-700 mb-1">نوع الخصم</label>
                                    <select name="discount_type" id="typeInput" required onchange="toggleDiscountTypeFields()" class="w-full rounded-xl border-slate-200 text-xs font-bold h-11 px-3.5 border bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                        <option value="percentage">نسبة مئوية (%)</option>
                                        <option value="fixed">مبلغ ثابت (YER)</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="valueInput" class="block text-xs font-extrabold text-slate-700 mb-1">قيمة الخصم</label>
                                    <input type="number" name="discount_value" id="valueInput" required min="0" step="0.01" class="w-full rounded-xl border-slate-200 text-xs font-bold h-11 px-3.5 border bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" placeholder="20">
                                </div>
                            </div>

                            <!-- Max Discount Cap (Visible when Percentage is selected) -->
                            <div id="maxDiscountCapContainer">
                                <label for="maxDiscountInput" class="block text-xs font-extrabold text-slate-700 mb-1">الحد الأقصى للخصم (Max Discount Cap YER)</label>
                                <input type="number" name="max_discount_amount" id="maxDiscountInput" min="0" step="1" class="w-full rounded-xl border-slate-200 text-xs font-bold h-11 px-3.5 border bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" placeholder="1000">
                                <p class="text-3xs text-slate-400 mt-1">اتركه فارغاً إذا كان الخصم بدون حد أقصى</p>
                            </div>

                            <!-- Scope / Restaurant selection -->
                            <div>
                                <label for="restaurantScopeInput" class="block text-xs font-extrabold text-slate-700 mb-1">نطاق كود الخصم (المطعم المستهدف)</label>
                                <select name="restaurant_id" id="restaurantScopeInput" class="w-full rounded-xl border-slate-200 text-xs font-bold h-11 px-3.5 border bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                    <option value="all">🌐 جميع المطاعم (المنصة العامة)</option>
                                    @foreach($restaurants ?? [] as $restaurant)
                                        <option value="{{ $restaurant->id }}">🏪 {{ $restaurant->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <hr class="border-slate-100 my-2">

                            <!-- Conditions & Restrictions -->
                            <div>
                                <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider mb-3">الحدود والشروط الاستهلاكية</h4>
                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <label for="minOrderInput" class="block text-3xs font-extrabold text-slate-600 mb-1">الحد الأدنى للطلب (YER)</label>
                                        <input type="number" name="min_order_amount" id="minOrderInput" min="0" step="1" class="w-full rounded-xl border-slate-200 text-xs font-bold h-10 px-3 border bg-white" placeholder="3000">
                                    </div>
                                    <div>
                                        <label for="limitInput" class="block text-3xs font-extrabold text-slate-600 mb-1">الحد الأقصى الكلي للاستخدام</label>
                                        <input type="number" name="max_usages" id="limitInput" min="1" class="w-full rounded-xl border-slate-200 text-xs font-bold h-10 px-3 border bg-white" placeholder="100">
                                    </div>
                                    <div>
                                        <label for="perUserLimitInput" class="block text-3xs font-extrabold text-slate-600 mb-1">الحد لكل مستخدم</label>
                                        <input type="number" name="per_user_limit" id="perUserLimitInput" min="1" value="1" class="w-full rounded-xl border-slate-200 text-xs font-bold h-10 px-3 border bg-white" placeholder="1">
                                    </div>
                                </div>
                            </div>

                            <!-- Active Checkbox -->
                            <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80">
                                <div>
                                    <div class="text-xs font-bold text-slate-900">تفعيل كود الخصم فور الحفظ</div>
                                    <div class="text-3xs text-slate-500">يكون الكود جاهزاً للتطبيق في السلة مباشرة</div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" id="discountActiveCheck" value="1" checked class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                </label>
                            </div>

                        </div>
                        <div class="bg-slate-50/80 px-6 py-4 border-t border-slate-100 flex justify-end gap-3">
                            <button type="button" onclick="closeDiscountModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-xs font-extrabold hover:bg-slate-100 transition-colors cursor-pointer">إلغاء</button>
                            <button type="submit" id="btnSaveDiscount" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-xs font-extrabold hover:bg-indigo-700 active:bg-indigo-800 transition-colors shadow-2xs cursor-pointer">حفظ كود الخصم</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Companion JS -->
    <script src="{{ asset('modules/admin/js/discounts.js') }}"></script>
</body>
</html>
