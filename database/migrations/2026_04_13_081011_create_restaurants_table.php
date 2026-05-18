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
        if (!Schema::hasTable('restaurants')) {
            Schema::create('restaurants', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('logo')->nullable();
                $table->text('description')->nullable();
                $table->string('address')->nullable();
                $table->string('phone')->nullable();
                $table->string('status')->default('active');
                $table->decimal('rating', 3, 2)->default(0.00);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
