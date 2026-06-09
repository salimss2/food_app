<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('scheduled_orders', function (Blueprint $table) {
            // إضافة الإحداثيات والبيانات المالية للطلبات المجدولة
            $table->decimal('latitude', 10, 8)->nullable()->after('total_amount');
            $table->decimal('longitude', 10, 8)->nullable()->after('latitude');

            $table->decimal('delivery_distance', 8, 2)->nullable()->after('longitude');
            $table->decimal('delivery_fee', 8, 2)->default(0)->after('delivery_distance');
            $table->decimal('driver_commission', 8, 2)->default(0)->after('delivery_fee');
            $table->decimal('platform_commission', 8, 2)->default(0)->after('driver_commission');
        });
    }

    public function down(): void
    {
        Schema::table('scheduled_orders', function (Blueprint $table) {
            $table->dropColumn([
                'latitude',
                'longitude',
                'delivery_distance',
                'delivery_fee',
                'driver_commission',
                'platform_commission'
            ]);
        });
    }
};