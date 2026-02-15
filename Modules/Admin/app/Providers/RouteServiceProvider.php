<?php

namespace Modules\Admin\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        parent::boot();

        $this->routes(function () {

            Route::middleware('web')
                ->group(module_path('Admin', '/routes/web.php'));

            Route::middleware('api')
                ->prefix('api')
                ->name('api.')
                ->group(module_path('Admin', '/routes/api.php'));

        });
    }
}
