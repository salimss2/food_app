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
        Schema::create('admin_offers', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // عنوان العرض مثل: خصم 25% على الحلويات
            $table->decimal('discount_percentage', 5, 2); // نسبة الخصم
            $table->unsignedBigInteger('restaurant_id')->nullable(); // إذا كان العرض لمطعم معين، أو NULL لكل المطاعم
            $table->date('expiry_date');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // الربط مع جدول المطاعم
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_offers');
    }
};
