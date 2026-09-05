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
    $routes = base_path('app/Modules/CRM/routes/api.php');
    if (file_exists($routes)) {
        $this->loadRoutesFrom($routes);
    }
    }
}
