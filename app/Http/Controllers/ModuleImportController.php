<?php

namespace App\Http\Controllers;

use App\Imports\ModuleExcelImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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


    try {
        // Try both ways

        $import = new ModuleExcelImport(
            $moduleId,
            auth()->id(),
            $request->boolean('strict_parent', true)
        );



        $result = Excel::queueImport($import, $request->file('file'));



        // Manually check jobs table
        $jobCount = \DB::table('jobs')->count();
     

        return response()->json([
            'message' => 'Import started successfully',
            'jobs_count' => $jobCount,
            'queue_connection' => config('queue.default')
        ]);

    } catch (\Exception $e) {
        \Log::error('Import failed completely', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'message' => 'Import failed',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
