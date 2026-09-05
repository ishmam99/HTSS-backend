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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         $modules = glob(base_path('app/Modules/*/database/migrations'), GLOB_ONLYDIR);

    foreach ($modules as $moduleMigrationPath) {
        $this->loadMigrationsFrom($moduleMigrationPath);
    }
    $routes = glob(base_path('app/Modules/*/routes/api.php'), GLOB_ONLYDIR);
    foreach ($routes as $route) {
        if (file_exists($route)) {
            $this->loadRoutesFrom($route);
        }
    }
    }
}
