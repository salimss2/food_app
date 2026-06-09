<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. (خطوة احترازية) تحويل أي بيانات غير متوافقة إلى الحالة الافتراضية لمنع خطأ البتر
        DB::statement("
            UPDATE orders 
            SET payment_status = 'pending_verification' 
            WHERE payment_status NOT IN (
                'pending_verification', 
                'pending_collection', 
                'completed', 
                'rejected',
                'canceled',
                'pending_refund',
                'refunded'
            )
        ");

        // 2. تطبيق التعديل وتوسيع قائمة الـ ENUM
        DB::statement("
            ALTER TABLE orders 
            MODIFY COLUMN payment_status ENUM(
                'pending_verification', 
                'pending_collection', 
                'completed', 
                'rejected',
                'canceled',
                'pending_refund',
                'refunded'
            ) NOT NULL DEFAULT 'pending_verification'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إرجاع القائمة إلى حالتها السابقة في حال أردنا التراجع عن المايجريشن

        // أولاً نرجع الحالات الجديدة إلى حالات قديمة مسموحة
        DB::statement("
            UPDATE orders 
            SET payment_status = 'completed' 
            WHERE payment_status IN ('refunded')
        ");

        DB::statement("
            UPDATE orders 
            SET payment_status = 'pending_verification' 
            WHERE payment_status IN ('canceled', 'pending_refund')
        ");

        // ثم نعيد هيكل العمود للقائمة القديمة
        DB::statement("
            ALTER TABLE orders 
            MODIFY COLUMN payment_status ENUM(
                'pending_verification', 
                'pending_collection', 
                'completed', 
                'rejected'
            ) NOT NULL DEFAULT 'pending_verification'
        ");
    }
};