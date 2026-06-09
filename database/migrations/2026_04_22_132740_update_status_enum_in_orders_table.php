<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. تنظيف وتحديث كل الحالات القديمة الممكنة إلى الحالات الجديدة
        \Illuminate\Support\Facades\DB::statement("UPDATE orders SET status = 'pending_admin_approval' WHERE status = 'pending'");
        \Illuminate\Support\Facades\DB::statement("UPDATE orders SET status = 'pending_driver_acceptance' WHERE status IN ('accepted', 'preparing')");
        \Illuminate\Support\Facades\DB::statement("UPDATE orders SET status = 'on_the_way' WHERE status = 'picked_up'");

        // 2. تعديل هيكل العمود بعد التأكد من عدم وجود أي كلمات غريبة في الجدول
        \Illuminate\Support\Facades\DB::statement("
        ALTER TABLE orders 
        MODIFY COLUMN status ENUM(
            'pending_admin_approval', 
            'pending_driver_acceptance', 
            'driver_assigned', 
            'ready_for_pickup', 
            'on_the_way', 
            'delivered', 
            'canceled'
        ) NOT NULL DEFAULT 'pending_admin_approval'
    ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
