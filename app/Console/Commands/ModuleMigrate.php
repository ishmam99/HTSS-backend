<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ModuleMigrate extends Command
{
    protected $signature = 'module:migrate {module}';
    protected $description = 'Run migrations for a specific module';

    public function handle()
    {
        $module = ucfirst($this->argument('module'));
        $path = base_path("app/Modules/{$module}/database/migrations");

        if (!is_dir($path)) {
            $this->error("Module [{$module}] does not exist or has no migrations.");
            return;
        }

        $this->info("Running migrations for module [{$module}]...");

        Artisan::call('migrate', [
            '--path' => $path,
            '--realpath' => true
        ]);

        $this->info("✅ Migrations for module [{$module}] completed.");
    }
}
