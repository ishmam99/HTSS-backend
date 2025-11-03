<?php

namespace Modules\CRM\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\CRM\Models\Module;
use Modules\CRM\Models\Record;
use Modules\CRM\Models\RecordValue;

class RecordController extends Controller
{
    public function index(Module $module)
    {
        // $records = Record::with(['values.field'])
        //     ->where('module_id', $module->id)
        //     ->get();

        // $result = $records->map(function ($rec) {
        //     $data = ['id' => $rec->id];
        //     foreach ($rec->values as $v) {
        //         $data[$v->field->name] = $v->value;
        //     }
        //     return $data;
        // });

        // return response()->json($result);
         $records = $module->records()
            ->with(['values.field'])
            ->get();

        return response()->json($records);
    }
public function store(Request $request, Module $module)
{
    DB::beginTransaction();

    try {
        $record = Record::create([
            'module_id' => $module->id,
            'created_by' => auth()->id(),
        ]);

        $insertData = [];
        $timestamp = now();

        foreach ($request->input('fields', []) as $fieldData) {

            if (isset($fieldData['field_id']) && isset($fieldData['value'])) {
                $insertData[] = [
                    'record_id' => $record->id,
                    'field_id' => $fieldData['field_id'],
                    'value' => $fieldData['value'],
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }
        }

        if (!empty($insertData)) {
            RecordValue::insert($insertData);
        }

        DB::commit();

        return response()->json($record->load('values.field'));
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Failed to create record',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function convertModule($recordId)
{
    
    $module = Module::where('name','Accounts')->first();
    if(!$module){
        return response()->json(['message'=>'Accounts module not found.'],400);
    }
    $record = Record::where('id',$recordId)->update(['module_id'=>$module->id]);

    return response()->json(['status'=>true,'message'=>'Record converted to Accounts module successfully.'],200);
}


}
