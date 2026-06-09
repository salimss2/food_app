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
        Schema::create('scheduled_orders', function (Blueprint $table) {
            $table->id();
            
            // ربط الطلب المجدول بالمستخدم الحالي
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); 
            
            // رقم الطلب الظاهر في واجهة المستخدم (مثل: 465841)
            $table->string('order_number')->unique(); 
            
            // عدد العناصر في الطلب
            $table->integer('items_count')->default(1); 
            
            // المجموع الكلي (مثل: 2000)
            $table->decimal('total_amount', 10, 2); 
            
            // تاريخ ووقت التوصيل المجدول
            $table->dateTime('scheduled_at'); 
            
            // حالة الطلب (مجدول، ملغي، مكتمل) ليتوافق مع أزرار الإلغاء والتعديل
            $table->enum('status', ['scheduled', 'cancelled', 'completed'])->default('scheduled'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_orders');
    }
};