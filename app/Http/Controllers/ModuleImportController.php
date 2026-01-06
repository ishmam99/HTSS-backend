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

        // ADD DEBUG LOGGING
        Log::info('=== IMPORT DEBUG INFO ===');
        Log::info('Queue connection: ' . config('queue.default'));
        Log::info('ENV QUEUE_CONNECTION: ' . env('QUEUE_CONNECTION'));
        Log::info('File: ' . $request->file('file')->getClientOriginalName());
        Log::info('Module ID: ' . $moduleId);
        Log::info('User ID: ' . auth()->id());

        try {
            Excel::queueImport(
                new ModuleExcelImport(
                    $module,
                    auth()->id(),
                    $request->boolean('strict_parent', true)
                ),
                $request->file('file')
            );

            Log::info('Excel::queueImport called successfully');

            return response()->json([
                'message' => 'Import started successfully',
                'debug' => [
                    'queue_connection' => config('queue.default'),
                    'env_queue' => env('QUEUE_CONNECTION')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Import failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
