<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->routes(function () {
            // Default web routes
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // Backoffice routes
            Route::middleware('web')
                ->prefix('backoffice')
                ->name('backoffice.')
                ->group(base_path('routes/backoffice.php'));
        });
    }
}
