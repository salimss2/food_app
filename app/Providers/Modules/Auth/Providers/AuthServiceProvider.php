<?php

namespace App\Providers\Modules\Auth\Providers;


// تأكد من إضافة هذا السطر فوق
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // --- أضف هذه السطور السحرية هنا ---
        Gate::before(function ($user, $ability) {
            // إذا كان المستخدم يمتلك دور Admin، افتح له كل شيء
            return $user->hasRole('System Admin') ? true : null;
        });
        // ------------------------------------
    }
}
