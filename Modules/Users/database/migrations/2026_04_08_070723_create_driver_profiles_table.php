<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('driver_profiles', function (Blueprint $table) {
            $table->id();
            // الربط بجدول المستخدمين الأساسي
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // بيانات الموصل
            $table->string('id_number')->nullable(); 
            $table->string('address')->nullable(); 
            $table->text('avatar_url')->nullable(); 

            // بيانات المركبة 
            $table->string('vehicle_model')->nullable();
            $table->string('vehicle_plate')->nullable();
            $table->string('vehicle_vin')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('driver_profiles');
    }
};