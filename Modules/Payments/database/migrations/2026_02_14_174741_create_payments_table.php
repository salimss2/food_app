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
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); // يفضل استخدام id() القياسي لسهولة الربط
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->enum('payment_type', ['cash', 'online']);
            $table->string('bank_name')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->string('currency_type', 10)->default('YER');
            $table->enum('payment_status', ['paid', 'pending', 'failed'])->default('pending');
            $table->timestamp('payment_date')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
