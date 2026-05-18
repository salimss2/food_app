<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('discount_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // كود الخصم مثل WELCOME20
            $table->enum('discount_type', ['percentage', 'fixed']); // نسبة مئوية أو مبلغ ثابت
            $table->decimal('discount_value', 10, 2); // قيمة الخصم (20 مثلاً)
            $table->decimal('min_order_amount', 10, 2)->default(0.00); // الحد الأدنى للطلب
            $table->integer('max_usages')->default(100); // الحد الأقصى للاستخدام الكلي
            $table->integer('used_count')->default(0); // عدد مرات الاستخدام الفعلية
            $table->date('expiry_date'); // تاريخ انتهاء الصلاحية
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_codes');
    }
};
