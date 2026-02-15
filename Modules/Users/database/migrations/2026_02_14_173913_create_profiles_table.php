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
        // 1. جدول البروفايل
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('avatar')->nullable();
            $table->string('address')->nullable();
            $table->json('preferences')->nullable();
            $table->timestamps();
        });

        // 2. جدول توافر السائقين
        Schema::create('driver_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['available', 'unavailable'])->default('available');
            $table->timestamp('last_updated')->nullable();
            $table->timestamps();
        });

        // 3. جدول وثائق السائق
        Schema::create('driver_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');
            $table->string('document_type'); // ID, License, etc.
            $table->string('file_path');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_documents');
        Schema::dropIfExists('driver_availability');
        Schema::dropIfExists('profiles');
    }
};
