<?php

namespace Modules\CRM\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CRM\Models\RecordValue;

class RecordValueController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'CRM RecordValueController works!']);
    }
    public function bulkUpdateOrCreate(Request $request)
{
    $validated = $request->validate([
        'record_ids' => 'required|array',
        'record_ids.*' => 'required',
        'field_id'  => 'required|required',
        'value'     => 'nullable|string',
    ]);

    $results = [];

    foreach ($validated['record_ids'] as $recordId) {
        $record = RecordValue::updateOrCreate(
            ['record_id' => $recordId, 'field_id' => $validated['field_id']],
            ['value'     => $validated['value']]
        );

        $results[] = [
            'record_id' => $recordId,
            'action' => $record->wasRecentlyCreated ? 'created' : 'updated'
        ];
    }

    return response()->json([
        'message' => 'Bulk update/create completed',
        'results' => $results
    ]);
}

}
