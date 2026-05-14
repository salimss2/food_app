<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\Kreait\Laravel\Firebase\FirebaseProjectManager::class, function ($app) {
            return new \App\Support\Firebase\CustomFirebaseProjectManager($app, $app->make('config'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
