<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeModuleController extends Command
{
    protected $signature = 'make:module:controller {module} {name}';
    protected $description = 'Create a new controller inside a specific module';

    public function handle()
    {
       $module = ucfirst($this->argument('module'));
        $name = ucfirst($this->argument('name'));

        $modulePath = base_path("app/Modules/{$module}/Controllers");
        $path = $modulePath . "/{$name}.php";


        if (!File::exists(base_path("app/Modules/{$module}"))) {
            $this->error("Module [{$module}] does not exist!");
            return;
        }
          if (!File::exists($modulePath)) {
            File::makeDirectory($modulePath, 0755, true);
        }
    if (File::exists($path)) {
            $this->error("Controller [{$name}] already exists in module [{$module}]!");
            return;
        }


        $content = <<<PHP
        <?php

        namespace Modules\\{$module}\\Controllers;

        use App\\Http\\Controllers\\Controller;
        use Illuminate\\Http\\Request;

        class {$name} extends Controller
        {
            public function index()
            {
                return response()->json(['message' => '{$module} {$name} works!']);
            }
        }
        PHP;

        File::put($path, $content);

        $this->info("✅ Controller [{$name}] created successfully in module [{$module}]");
    }
}
