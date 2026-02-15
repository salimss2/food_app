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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            // الربط بالمطعم: كل مطعم لديه عدة أقسام في القائمة (مثلاً مطعم "X" لديه قسم "عصائر")
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');

            $table->string('name'); // اسم القسم (مثال: وجبات سريعة، إفطار، تحلية)
            $table->string('description')->nullable(); // وصف اختياري للقسم
            $table->integer('sort_order')->default(0); // ترتيب الظهور (لكي تضع المقبلات قبل التحلية مثلاً)
            $table->boolean('is_active')->default(true); // لتعطيل قسم كامل مؤقتاً

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            Schema::dropIfExists('menus');
        });
    }
};
