<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
class MakeModuleModel extends Command
{
  protected $signature = 'make:module:model
                        {module}
                        {name}
                        {--m|migration}
                        {--c|controller}';
    protected $description = 'Create a new model inside a specific module';

    public function handle()
    {
        $module = ucfirst($this->argument('module'));
        $name = ucfirst($this->argument('name'));

        $modulePath = base_path("app/Modules/{$module}/Models");
        $path = $modulePath . "/{$name}.php";


       if ($this->option('migration')) {
            // Get the model name
            $modelName = $this->argument('name');

            // Convert model name to snake_case and plural form
            $tableName = Str::snake(Str::pluralStudly($modelName));

            // Call the module migration generator
            $this->call('make:module:migration', [
                'module' => $module,
                'name' => $tableName,
            ]);
        }

        if ($this->option('controller')) {
            // Call make:module:controller command
            $this->call('make:module:controller', [
                'module' => $module,
                'name' => $name . 'Controller'
            ]);
        }


        if (!File::exists(base_path("app/Modules/{$module}"))) {
            $this->error("Module [{$module}] does not exist!");
            return;
        }

        if (!File::exists($modulePath)) {
            File::makeDirectory($modulePath, 0755, true);
        }

        if (File::exists($path)) {
            $this->error("Model [{$name}] already exists in module [{$module}]!");
            return;
        }

        $content = <<<PHP
<?php

namespace Modules\\{$module}\\Models;

use Illuminate\\Database\\Eloquent\\Model;

class {$name} extends Model
{
    protected \$guarded = [];
}
PHP;

        File::put($path, $content);

        $this->info("✅ Model [{$name}] created successfully in module [{$module}]");
    }
}
