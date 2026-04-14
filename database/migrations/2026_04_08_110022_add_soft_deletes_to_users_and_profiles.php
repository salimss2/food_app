<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->softDeletes(); // يضيف عمود deleted_at
    });

    Schema::table('driver_profiles', function (Blueprint $table) {
        $table->softDeletes();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_and_profiles', function (Blueprint $table) {
            //
        });
    }
};
