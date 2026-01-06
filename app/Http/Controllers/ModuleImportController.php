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

    \Log::info('=== IMPORT DEBUG DETAILED ===');
    \Log::info('Before Excel::queueImport');

    // Test if we can dispatch a simple job
    \Illuminate\Support\Facades\Queue::after(function ($event) {
        \Log::info('Job was pushed to queue', [
            'job' => get_class($event->job),
            'id' => $event->job->getJobId()
        ]);
    });

    try {
        // Try both ways
        \Log::info('Calling Excel::queueImport...');

        $import = new ModuleExcelImport(
            $moduleId,
            auth()->id(),
            $request->boolean('strict_parent', true)
        );

        \Log::info('Import object created', [
            'import_class' => get_class($import),
            'module' => $module->id,
            'user' => auth()->id()
        ]);

        $result = Excel::queueImport($import, $request->file('file'));

        \Log::info('Excel::queueImport returned', [
            'result_type' => gettype($result),
            'result' => $result
        ]);

        // Manually check jobs table
        $jobCount = \DB::table('jobs')->count();
        \Log::info('Jobs table count after dispatch: ' . $jobCount);

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
