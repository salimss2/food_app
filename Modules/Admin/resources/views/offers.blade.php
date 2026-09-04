<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>إدارة العروض والوجبات الترويجية - لوحة التحكم</title>

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
                        sans: ['Cairo', 'Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Cairo Font (Arabic RTL) -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('modules/admin/css/app.css') }}">
    <style>
        body {
            font-family: 'Cairo', sans-serif !important;
        }

        .hidden-el {
            display: none !important;
        }

        /* Custom Toggle Switch */
        .toggle-checkbox:checked {
            right: 1.25rem;
            border-color: #10b981;
        }

        .toggle-checkbox:checked + .toggle-label {
            background-color: #10b981;
        }

        html[dir="rtl"] .toggle-checkbox:checked {
            left: 1.25rem;
            right: auto;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 font-sans antialiased overflow-hidden flex h-screen">

    @include('admin::layouts.partials.sidebar')

    <!-- Layout Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300">

        @include('admin::layouts.partials.header')

        <!-- Main Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto w-full space-y-8">

            <!-- Toast Alert -->
            <div id="toast" class="hidden-el fixed bottom-5 left-5 z-50 rounded-2xl bg-emerald-500 text-white p-4 shadow-xl border border-emerald-400 transition-all duration-300 flex items-center gap-3">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="text-sm font-bold" id="toastMessage">تم العملية بنجاح</span>
            </div>

            <!-- Page Action Header -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2zm0 0h4.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 01.439 1.061v4.379a1.5 1.5 0 01-.44 1.06l-2.12 2.122a1.5 1.5 0 01-1.061.44H12m0 0H7.121a1.5 1.5 0 01-1.06-.44l-2.122-2.12A1.5 1.5 0 013.5 13.879V9.5a1.5 1.5 0 01.44-1.06l2.12-2.122A1.5 1.5 0 017.121 5.88H12"></path></svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-black text-slate-900 tracking-tight">إدارة العروض والوجبات الترويجية</h1>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">متابعة البانرات الترويجية، الخصومات العامة، وعروض الوجبات المخفضة للمطاعم</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                    <!-- Filters -->
                    <div class="flex items-center gap-2 flex-1 sm:flex-initial">
                        <select id="offerFilterStatus" onchange="filterOffersTable()" class="rounded-xl border-slate-200 text-xs font-bold py-2.5 px-3 bg-slate-50 text-slate-700 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 border transition-all">
                            <option value="ALL">جميع الحالات</option>
                            <option value="active">نشط (Live)</option>
                            <option value="inactive">غير نشط / منتهي</option>
                        </select>

                        <select id="offerFilterType" onchange="filterOffersTable()" class="rounded-xl border-slate-200 text-xs font-bold py-2.5 px-3 bg-slate-50 text-slate-700 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 border transition-all">
                            <option value="ALL">جميع الأنواع</option>
                            <option value="banner">بانر ترويجي عام</option>
                            <option value="direct_cart">وجبة مخفضة تضاف للسلة</option>
                        </select>
                    </div>

                    <!-- Create Offer Button -->
                    <button onclick="openCreateOfferModal()" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500/20 transition-all whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>إنشاء عرض ترويجي جديد +</span>
                    </button>
                </div>
            </div>

            <!-- KPI Metric Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Total Offers -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">إجمالي العروض والبانرات</p>
                        <h3 class="text-3xl font-black text-slate-900">{{ number_format($totalOffersCount) }}</h3>
                        <p class="text-xs text-slate-400 font-medium mt-1">تشمل البانرات العامة وعروض المطاعم</p>
                    </div>
                    <div class="w-13 h-13 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shadow-2xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                    </div>
                </div>

                <!-- Active Live Offers -->
                <div class="bg-white rounded-2xl p-6 border border-emerald-200/80 shadow-xs flex items-center justify-between relative overflow-hidden">
                    <div class="absolute top-0 right-0 left-0 h-1 bg-emerald-500"></div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider">العروض النشطة الحالية</p>
                        </div>
                        <h3 class="text-3xl font-black text-slate-900">{{ number_format($liveOffersCount) }}</h3>
                        <p class="text-xs text-emerald-600 font-medium mt-1">تظهر حالياً في التطبيق للعملاء</p>
                    </div>
                    <div class="w-13 h-13 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 shadow-2xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                </div>

                <!-- Expired Offers -->
                <div class="bg-white rounded-2xl p-6 border border-amber-200/80 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-amber-700 uppercase tracking-wider mb-1">العروض المنتهية / المعطلة</p>
                        <h3 class="text-3xl font-black text-slate-900">{{ number_format($expiredOffersCount) }}</h3>
                        <p class="text-xs text-amber-600 font-medium mt-1">عروض متوقفة أو تجاوزت الصلاحية</p>
                    </div>
                    <div class="w-13 h-13 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 shadow-2xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- SECTION 1: General Promotional Banners -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-slate-50/50">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                            <span>العروض والبانرات الترويجية العامة</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-indigo-100 text-indigo-700 border border-indigo-200">{{ count($offers) }}</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">البانرات الترويجية الرئيسية المعروضة في شريط التطبيق العلوي</p>
                    </div>
                </div>

                <div class="overflow-x-auto w-full">
                    <table class="w-full text-right text-sm text-slate-600">
                        <thead class="bg-slate-50 text-slate-700 font-bold text-xs uppercase border-b border-slate-200/80">
                            <tr>
                                <th scope="col" class="px-6 py-4">الصورة المصغرة</th>
                                <th scope="col" class="px-6 py-4">عنوان العرض والتفاصيل</th>
                                <th scope="col" class="px-6 py-4">النطاق / المطعم</th>
                                <th scope="col" class="px-6 py-4">نوع الخصم والقيمة</th>
                                <th scope="col" class="px-6 py-4">سلوك النقر</th>
                                <th scope="col" class="px-6 py-4">فترة الصلاحية</th>
                                <th scope="col" class="px-6 py-4">التفعيل الفوري</th>
                                <th scope="col" class="px-6 py-4 text-left">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody id="offersTableBody" class="divide-y divide-slate-100 bg-white">
                            @forelse($offers as $offer)
                                @php
                                    $isPast = $offer->expiry_date && \Carbon\Carbon::parse($offer->expiry_date)->isPast();
                                    $isActive = $offer->status === 'active' && !$isPast;
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition-colors offer-row" data-status="{{ $offer->status }}" data-type="{{ $offer->type ?? 'banner' }}">
                                    <!-- Banner Image Thumbnail -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="relative group cursor-pointer w-20 h-11 rounded-xl overflow-hidden border border-slate-200 shadow-2xs" onclick="previewBannerImage('{{ $offer->image_url }}', '{{ addslashes($offer->title) }}')">
                                            <img src="{{ $offer->image_url }}" alt="{{ $offer->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                            <div class="absolute inset-0 bg-slate-900/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Title & Description -->
                                    <td class="px-6 py-4">
                                        <div class="font-extrabold text-slate-900 text-sm mb-0.5">{{ $offer->title }}</div>
                                        <div class="text-xs text-slate-500 max-w-xs truncate" title="{{ $offer->description }}">{{ $offer->description ?? 'لا يوجد وصف تفصيلي' }}</div>
                                    </td>

                                    <!-- Target / Restaurant -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($offer->restaurant)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                                {{ $offer->restaurant->name }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                                🌐 جميع المطاعم (المنصة)
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Discount Type & Value -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($offer->offer_price)
                                            <span class="inline-flex items-center px-3 py-1 rounded-xl font-bold text-xs bg-purple-50 text-purple-700 border border-purple-200">
                                                سعر خاص {{ number_format($offer->offer_price) }} YER
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-xl font-bold text-xs bg-blue-50 text-blue-700 border border-blue-200">
                                                خصم {{ number_format($offer->discount_percentage, 0) }}%
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Click Action / Entity -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(($offer->click_action ?? 'restaurant') === 'cart')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                🛒 إضافة للسلة مباشرة
                                            </span>
                                        @elseif(($offer->click_action ?? 'restaurant') === 'coupon')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-pink-50 text-pink-700 border border-pink-200">
                                                🎟️ تطبيق كود خصم
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                                🏪 فتح قائمة المطعم
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Validity Period & Countdown -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-xs font-bold text-slate-700">
                                            {{ $offer->expiry_date ? \Carbon\Carbon::parse($offer->expiry_date)->format('Y-m-d') : 'مستمر بلا انتهاء' }}
                                        </div>
                                        @if($isPast)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-3xs font-extrabold bg-red-100 text-red-700 mt-1">🔴 منتهي</span>
                                        @elseif($offer->expiry_date)
                                            @php $daysLeft = now()->diffInDays(\Carbon\Carbon::parse($offer->expiry_date), false); @endphp
                                            @if($daysLeft <= 3 && $daysLeft >= 0)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-3xs font-extrabold bg-amber-100 text-amber-800 mt-1">⏳ ينتهي خلال {{ $daysLeft }} أيام</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-3xs font-extrabold bg-emerald-100 text-emerald-800 mt-1">🟢 ساري المفعول</span>
                                            @endif
                                        @endif
                                    </td>

                                    <!-- Live Toggle Switch -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" onchange="toggleOfferStatus({{ $offer->id }}, this)" class="sr-only peer" {{ $offer->status === 'active' ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                        </label>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 whitespace-nowrap text-left">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" onclick="editOffer({{ json_encode($offer) }})" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors" title="تعديل العرض">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            <button type="button" onclick="deleteOffer({{ $offer->id }})" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors" title="حذف العرض">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-slate-400 font-bold">
                                        لا توجد عروض ترويجية عامة حالياً
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(isset($offers) && $offers->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $offers->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>

            <!-- SECTION 2: Restaurant Combo Meals -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-slate-50/50">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                            <span>عروض الوجبات المجمعة والمفضلة للمطاعم (Combo Deals)</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-700 border border-emerald-200">{{ count($restaurantCombos) }}</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">عروض الكومبو والوجبات المجمعة المنشأة بواسطة المطاعم</p>
                    </div>
                </div>

                <div class="overflow-x-auto w-full">
                    <table class="w-full text-right text-sm text-slate-600">
                        <thead class="bg-slate-50 text-slate-700 font-bold text-xs uppercase border-b border-slate-200/80">
                            <tr>
                                <th scope="col" class="px-6 py-4">صورة الوجبة</th>
                                <th scope="col" class="px-6 py-4">اسم الوجبة والعرض</th>
                                <th scope="col" class="px-6 py-4">المطعم المالك</th>
                                <th scope="col" class="px-6 py-4">مقارنة الأسعار والتوفير</th>
                                <th scope="col" class="px-6 py-4">تاريخ الصلاحية</th>
                                <th scope="col" class="px-6 py-4">حالة التوفر</th>
                                <th scope="col" class="px-6 py-4 text-left">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($restaurantCombos as $combo)
                                @php
                                    $origPrice = $combo->original_price ?? ($combo->combo_price * 1.25);
                                    $savingsPercent = $origPrice > 0 ? round((($origPrice - $combo->combo_price) / $origPrice) * 100) : 0;
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <!-- Combo Image -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <img src="{{ $combo->restaurant?->logo_full_url ?? $combo->image_url ?? asset('assets/default-offer.png') }}" alt="{{ $combo->title }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-2xs" onerror="this.src='{{ asset('assets/default-offer.png') }}'">
                                    </td>

                                    <!-- Combo Title & Items -->
                                    <td class="px-6 py-4">
                                        <div class="font-extrabold text-slate-900 text-sm mb-0.5">{{ $combo->title }}</div>
                                        <div class="text-xs text-slate-500 max-w-md truncate">{{ $combo->description ?? 'وجبة مجمعة مميزة' }}</div>
                                    </td>

                                    <!-- Restaurant -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-xs">
                                                {{ mb_substr($combo->restaurant->name ?? 'M', 0, 1) }}
                                            </div>
                                            <span class="text-xs font-bold text-slate-900">{{ $combo->restaurant->name ?? 'مطعم غير معروف' }}</span>
                                        </div>
                                    </td>

                                    <!-- Price Comparison & Savings -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-slate-400 line-through font-medium">{{ number_format($origPrice) }} YER</span>
                                            <span class="text-sm font-black text-emerald-600 bg-emerald-50 border border-emerald-200 rounded-lg px-2.5 py-1">
                                                {{ number_format($combo->combo_price) }} YER
                                            </span>
                                            @if($savingsPercent > 0)
                                                <span class="px-2 py-0.5 bg-amber-100 text-amber-800 text-3xs font-black rounded-md">وفر {{ $savingsPercent }}%</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Expiry Date -->
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-slate-600">
                                        {{ $combo->end_date ? \Carbon\Carbon::parse($combo->end_date)->format('Y-m-d') : 'مستمر' }}
                                    </td>

                                    <!-- Availability Toggle Switch -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" onchange="toggleComboStatus({{ $combo->id }}, this)" class="sr-only peer" {{ ($combo->status ?? 'active') === 'active' ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                        </label>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 whitespace-nowrap text-left">
                                        <button type="button" onclick="deleteCombo({{ $combo->id }})" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors" title="حذف الوجبة">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-400 font-bold">
                                        لا توجد وجبات مجمعة للمطاعم حالياً
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(isset($restaurantCombos) && $restaurantCombos->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $restaurantCombos->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>

        </main>
    </div>

    <!-- Create / Edit Offer Modal (#offerModal) -->
    <div id="offerModal" class="fixed inset-0 z-50 hidden-el overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closeOfferModal()"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-6">
            <div class="relative transform overflow-hidden rounded-3xl bg-white text-right shadow-2xl transition-all sm:w-full sm:max-w-2xl border border-slate-100">
                
                <!-- Modal Header -->
                <div class="bg-slate-50/90 px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                            ✨
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-slate-900" id="offerModalTitle">إنشاء عرض ترويجي جديد</h3>
                            <p class="text-xs text-slate-500 font-medium">قم بتعبئة تفاصيل العرض الترويجي والخصم والمدة</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeOfferModal()" class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-slate-700 bg-white hover:bg-slate-100 rounded-full transition-colors border border-slate-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Form -->
                <form id="offerForm" action="{{ route('admin.offers.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="offerFormMethod" value="POST">

                    <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">

                        <!-- Offer Type Selector -->
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 mb-2">نوع العرض الترويجي</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative flex items-center p-3.5 rounded-2xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors bg-white">
                                    <input type="radio" name="type" value="banner" checked onclick="toggleOfferTypeFields('banner')" class="text-indigo-600 focus:ring-indigo-500">
                                    <div class="mr-3">
                                        <div class="text-xs font-bold text-slate-900">بانر ترويجي عام (Banner)</div>
                                        <div class="text-3xs text-slate-500">يعرض في شريط التطبيق العلوي</div>
                                    </div>
                                </label>
                                <label class="relative flex items-center p-3.5 rounded-2xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors bg-white">
                                    <input type="radio" name="type" value="direct_cart" onclick="toggleOfferTypeFields('direct_cart')" class="text-indigo-600 focus:ring-indigo-500">
                                    <div class="mr-3">
                                        <div class="text-xs font-bold text-slate-900">وجبة مخفضة تضاف للسلة</div>
                                        <div class="text-3xs text-slate-500">يرتبط بوجبة محددة بسعر ترويجي</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Offer Title & Description -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="offerTitleInput" class="block text-xs font-extrabold text-slate-700 mb-1">عنوان العرض الترويجي *</label>
                                <input type="text" name="title" id="offerTitleInput" required class="w-full rounded-xl border-slate-200 text-sm font-bold h-11 px-3.5 border focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" placeholder="مثال: خصم 25% على الوجبات العائلية">
                            </div>
                            <div>
                                <label for="offerClickActionInput" class="block text-xs font-extrabold text-slate-700 mb-1">سلوك النقر عند الضغط</label>
                                <select name="click_action" id="offerClickActionInput" class="w-full rounded-xl border-slate-200 text-xs font-bold h-11 px-3.5 border bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                    <option value="restaurant">فتح قائمة المطعم (Restaurant Menu)</option>
                                    <option value="cart">إضافة مباشرة للسلة (Direct Cart)</option>
                                    <option value="coupon">تطبيق كود خصم (Coupon Code)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="offerDescInput" class="block text-xs font-extrabold text-slate-700 mb-1">وصف العرض المختصر</label>
                            <textarea name="description" id="offerDescInput" rows="2" class="w-full rounded-xl border-slate-200 text-xs font-medium p-3 border focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" placeholder="اكتب وصفاً جذاباً للعميل..."></textarea>
                        </div>

                        <!-- Restaurant & Linked Meal Selection -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="offerRestaurantInput" class="block text-xs font-extrabold text-slate-700 mb-1">اختيار المطعم المرتبط</label>
                                <select name="restaurant_id" id="offerRestaurantInput" onchange="filterMealOptionsByRestaurant()" class="w-full rounded-xl border-slate-200 text-xs font-bold h-11 px-3.5 border bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                    <option value="all">🌐 جميع المطاعم (المنصة العامة)</option>
                                    @foreach($restaurants as $restaurant)
                                        <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="mealSelectContainer" class="hidden-el">
                                <label for="offerMealInput" class="block text-xs font-extrabold text-slate-700 mb-1">اختيار الوجبة المرتبطة</label>
                                <select name="meal_id" id="offerMealInput" onchange="autoFillMealPrice()" class="w-full rounded-xl border-slate-200 text-xs font-bold h-11 px-3.5 border bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                    <option value="">اختر وجبة...</option>
                                    @foreach($meals as $meal)
                                        <option value="{{ $meal->id }}" data-price="{{ $meal->price }}" data-restaurant="{{ $meal->restaurant_id }}">{{ $meal->name }} ({{ number_format($meal->price) }} YER)</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Pricing Fields -->
                        <div class="grid grid-cols-3 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
                            <div>
                                <label for="offerDiscountInput" class="block text-3xs font-extrabold text-slate-600 mb-1">نسبة الخصم %</label>
                                <input type="number" name="discount_percentage" id="offerDiscountInput" min="0" max="100" step="1" class="w-full rounded-xl border-slate-200 text-xs font-bold h-10 px-3 border bg-white" placeholder="25">
                            </div>
                            <div>
                                <label for="offerOrigPriceInput" class="block text-3xs font-extrabold text-slate-600 mb-1">السعر الأصلي (YER)</label>
                                <input type="number" name="original_price" id="offerOrigPriceInput" min="0" step="1" class="w-full rounded-xl border-slate-200 text-xs font-bold h-10 px-3 border bg-white" placeholder="3500">
                            </div>
                            <div>
                                <label for="offerPriceInput" class="block text-3xs font-extrabold text-slate-600 mb-1">سعر العرض المخفض (YER)</label>
                                <input type="number" name="offer_price" id="offerPriceInput" min="0" step="1" class="w-full rounded-xl border-slate-200 text-xs font-bold h-10 px-3 border bg-white" placeholder="2700">
                            </div>
                        </div>

                        <!-- Banner Image Upload with Live Preview Box -->
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 mb-1">الصورة الترويجية (Banner Image)</label>
                            <div class="flex items-center gap-4">
                                <div class="w-28 h-16 rounded-2xl bg-slate-100 border-2 border-dashed border-slate-200 overflow-hidden flex items-center justify-center text-slate-400 relative">
                                    <img id="bannerImagePreview" src="" alt="Preview" class="hidden-el w-full h-full object-cover">
                                    <span id="bannerImagePlaceholder" class="text-3xs font-bold text-center px-2">معاينة الصورة</span>
                                </div>
                                <input type="file" name="banner_image" id="bannerImageInput" accept="image/*" onchange="handleImagePreview(this)" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                        </div>

                        <!-- Date Range Picker -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="offerStartDateInput" class="block text-xs font-extrabold text-slate-700 mb-1">تاريخ البدء</label>
                                <input type="date" name="start_date" id="offerStartDateInput" value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-slate-200 text-xs font-bold h-10 px-3 border bg-white">
                            </div>
                            <div>
                                <label for="offerExpiryInput" class="block text-xs font-extrabold text-slate-700 mb-1">تاريخ الانتهاء</label>
                                <input type="date" name="expiry_date" id="offerExpiryInput" class="w-full rounded-xl border-slate-200 text-xs font-bold h-10 px-3 border bg-white">
                            </div>
                        </div>

                        <!-- Status Toggle -->
                        <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80">
                            <div>
                                <div class="text-xs font-bold text-slate-900">تفعيل العرض الترويجي مباشرة</div>
                                <div class="text-3xs text-slate-500">سيتم نشر العرض في التطبيق فور الحفظ</div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" id="offerActiveCheck" value="1" checked class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            </label>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-slate-50/90 px-6 py-4 border-t border-slate-100 flex justify-end gap-3">
                        <button type="button" onclick="closeOfferModal()" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-bold text-xs transition-colors">إلغاء</button>
                        <button type="submit" id="btnSaveOffer" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition-all">حفظ العرض</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal (#imagePreviewModal) -->
    <div id="imagePreviewModal" class="fixed inset-0 z-50 hidden-el overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-xs transition-opacity" onclick="closeImagePreviewModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-3xl bg-white shadow-2xl transition-all p-2 max-w-3xl w-full border border-slate-100">
                <div class="flex justify-between items-center p-3 border-b border-slate-100">
                    <span class="text-sm font-bold text-slate-900" id="previewModalTitle">معاينة الصورة</span>
                    <button type="button" onclick="closeImagePreviewModal()" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-700 bg-slate-100 rounded-full">✕</button>
                </div>
                <div class="p-2 flex justify-center">
                    <img id="modalFullImage" src="" alt="Full Preview" class="max-h-[75vh] w-auto object-contain rounded-2xl">
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('modules/admin/js/app.js') }}"></script>
    <script src="{{ asset('modules/admin/js/offers.js') }}"></script>
</body>

</html>