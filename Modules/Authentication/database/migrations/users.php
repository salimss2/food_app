public function up()
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        // الحقل الأهم لتمييز الأنواع الأربعة
        $table->enum('role', ['customer', 'delivery_boy', 'restaurant_owner', 'admin'])->default('customer');
        $table->string('phone')->nullable();
        $table->timestamps();
    });
}
