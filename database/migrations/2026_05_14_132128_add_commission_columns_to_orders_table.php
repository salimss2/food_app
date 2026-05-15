<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // إضافة الأعمدة بعد عمود total الحالي للترتيب
            $table->decimal('delivery_distance', 8, 2)->nullable()->after('total')->comment('المسافة بالكيلومتر');
            $table->decimal('delivery_fee', 8, 2)->default(0)->after('delivery_distance')->comment('رسوم التوصيل الإجمالية');
            $table->decimal('driver_commission', 8, 2)->default(0)->after('delivery_fee')->comment('ربح الموصل');
            $table->decimal('platform_commission', 8, 2)->default(0)->after('driver_commission')->comment('ربح المنصة من التوصيل');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_distance',
                'delivery_fee',
                'driver_commission',
                'platform_commission'
            ]);
        });
    }
};