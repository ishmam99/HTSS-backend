<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeModule extends Command
{
    protected $signature = 'make:module {name}';
    protected $description = 'Create a new module folder structure';

    public function handle()
    {
        $name = ucfirst($this->argument('name'));
        $modulePath = base_path("app/Modules/{$name}");

        if (File::exists($modulePath)) {
            $this->error("Module [{$name}] already exists!");
            return;
        }

        $directories = [
            "{$modulePath}/Controllers",
            "{$modulePath}/Models",
            "{$modulePath}/Requests",
            "{$modulePath}/database/migrations",
            "{$modulePath}/routes",
        ];

        foreach ($directories as $dir) {
            File::makeDirectory($dir, 0755, true);
        }

       File::put("{$modulePath}/routes/api.php", "<?php

use Illuminate\\Support\\Facades\\Route;
use Modules\\{$name}\\src\\Controllers\\LeadController;

Route::prefix('api')->middleware('api')->group(function () {
    Route::prefix('" . strtolower($name) . "')->group(function () {
       //add api routes for module
    });
});
");

        $this->info("✅ Module [{$name}] created successfully at: modules/{$name}");
    }
}
