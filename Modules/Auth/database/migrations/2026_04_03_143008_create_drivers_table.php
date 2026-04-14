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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->string('phone')->unique(); 
            $table->string('email')->nullable()->unique(); 
            $table->string('id_number')->nullable(); 
            $table->string('address')->nullable(); 
            $table->string('password');
            
            // 🔥 الحقل الجديد لصورة السائق
            $table->text('avatar_url')->nullable(); 

            // بيانات المركبة 
            $table->string('vehicle_model')->nullable();
            $table->string('vehicle_plate')->nullable();
            $table->string('vehicle_vin')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
