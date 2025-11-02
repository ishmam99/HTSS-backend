<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleMigration extends Command
{
    protected $signature = 'make:module:migration {module} {name}';
    protected $description = 'Create a new migration inside a specific module';

    public function handle()
    {
        $module = ucfirst($this->argument('module'));
        $name = $this->argument('name'); // migration name

        $migrationPath = base_path("app/Modules/{$module}/database/migrations");

        if (!File::exists(base_path("app/Modules/{$module}"))) {
            $this->error("Module [{$module}] does not exist!");
            return;
        }

        if (!File::exists($migrationPath)) {
            File::makeDirectory($migrationPath, 0755, true);
        }

        $timestamp = date('Y_m_d_His');
        $file = $migrationPath . "/{$timestamp}_create_{$name}_table.php";

        $className = Str::studly($name);

        $content = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{$name}', function (Blueprint \$table) {
            \$table->id();
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$name}');
    }
};
PHP;

        File::put($file, $content);

        $this->info("✅ Migration [{$file}] created successfully in module [{$module}]");
    }
}
