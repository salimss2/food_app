<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // إضافة إحداثيات موقع الزبون (وجهة التوصيل)
            // جعلناها nullable لتجنب الأخطاء مع أي طلبات سابقة في قاعدة البيانات
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // إضافة أجرة/ربح الموصل
            // نستخدم decimal للأمور المالية لضمان الدقة (8 أرقام إجمالاً، منها 2 بعد الفاصلة العشرية)
            $table->decimal('driver_earning', 8, 2)->default(0)->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // حذف الحقول في حال أردت التراجع
            $table->dropColumn(['latitude', 'longitude', 'driver_earning']);
        });
    }
};