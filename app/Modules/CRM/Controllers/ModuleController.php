<?php

namespace Modules\CRM\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\CRM\Models\Module;
use Modules\CRM\Models\Record;
use Modules\CRM\Models\RecordUserAssignment;

class ModuleController extends Controller
{
   public function index()
   {
     $modules = Module::withCount('fields')->get();
     return response()->json($modules);
   }
public function stats(Request $request)
{
    // Start query builder
    $query = Record::query();

    // Restrict for sales-manager / sales-executive
    if (in_array(auth()->user()->role, ['sales-manager', 'sales-executive', 'manager-cs', 'manager-sales', 'executive-cs', 'executive-sales'])) {
        $mine = RecordUserAssignment::where('user_id', auth()->id())
            ->pluck('record_id');

        $query->whereIn('records.id', $mine);
    }

    // Group by modules and count records
    $stats = $query
        ->join('modules', 'records.module_id', '=', 'modules.id')
        ->select(
            'modules.id as module_id',
            'modules.name as module_name',
            DB::raw('COUNT(records.id) as total_records')
        )
        ->groupBy('modules.id', 'modules.name')
        ->get();

    return response()->json($stats);
}

}
