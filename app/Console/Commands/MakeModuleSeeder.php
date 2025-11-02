<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleSeeder extends Command
{
    protected $signature = 'make:module:seeder {module} {name}';
    protected $description = 'Create a new seeder inside a specific module';

    public function handle()
    {
        $module = ucfirst($this->argument('module'));
        $name = Str::studly($this->argument('name'));

        $seederPath = base_path("app/Modules/{$module}/database/seeders");

        // Check module existence
        if (!File::exists(base_path("app/Modules/{$module}"))) {
            $this->error("❌ Module [{$module}] does not exist!");
            return;
        }

        // Create seeder directory if not exists
        if (!File::exists($seederPath)) {
            File::makeDirectory($seederPath, 0755, true);
        }

        $file = "{$seederPath}/{$name}.php";

        // Prevent overwriting existing seeder
        if (File::exists($file)) {
            $this->error("⚠️ Seeder [{$name}] already exists in module [{$module}]!");
            return;
        }

        // Generate proper seeder class
        $content = <<<PHP
<?php

namespace Modules\\{$module}\\database\\seeders;

use Illuminate\\Database\\Seeder;
use Illuminate\\Support\\Facades\\DB;

class {$name} extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Example:
        // DB::table('users')->insert([
        //     'name' => 'John Doe',
        // ]);
    }
}
PHP;

        File::put($file, $content);

        $this->info("✅ Seeder [{$name}] created successfully in module [{$module}]");
    }
}
