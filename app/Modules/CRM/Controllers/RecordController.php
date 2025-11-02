<?php

namespace Modules\CRM\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CRM\Models\Module;
use Modules\CRM\Models\Record;
use Modules\CRM\Models\RecordValue;

class RecordController extends Controller
{
    public function index(Module $module)
    {
        $records = Record::with(['values.field'])
            ->where('module_id', $module->id)
            ->get();

        $result = $records->map(function ($rec) {
            $data = ['id' => $rec->id];
            foreach ($rec->values as $v) {
                $data[$v->field->name] = $v->value;
            }
            return $data;
        });

        return response()->json($result);
    }

    public function store(Request $request, Module $module)
{
    $fields = $module->fields;
    $record = Record::create([
        'module_id' => $module->id,
        'created_by' => auth()->id(),
    ]);

    foreach ($fields as $field) {
        if ($request->has($field->name)) {
            RecordValue::create([
                'record_id' => $record->id,
                'field_id' => $field->id,
                'value' => $request->input($field->name),
            ]);
        }
    }

    return response()->json($record->load('values.field'));
}

}
