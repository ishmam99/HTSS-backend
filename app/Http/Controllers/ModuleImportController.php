<?php

namespace App\Http\Controllers;

use App\Imports\ModuleExcelImport;
use Illuminate\Container\Attributes\DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\CRM\Models\Module;

class ModuleImportController extends Controller
{
    public function import(Request $request, $moduleId)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
            'strict_parent' => 'boolean',
        ]);

        $module = Module::findOrFail($moduleId);

        Excel::queueImport(
            new ModuleExcelImport(
                $module,
                auth()->id(),
                $request->boolean('strict_parent', true)
            ),
            $request->file('file')
        );

        return response()->json([
            'message' => 'Import started successfully',
        ]);
    }

    public function importErrors($moduleId)
    {
        return DB::table('import_errors')
            ->where('module_id', $moduleId)
            ->latest()
            ->paginate(50);
    }
}
