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
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();
            $table->enum('payment_type', ['cash', 'bank_transfer']);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency_type', 10)->default('YER');
            $table->enum('payment_status', [
                'pending',
                'confirmed',
                'rejected',
                'cash_pending',
                'paid'
            ])->default('pending');
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
