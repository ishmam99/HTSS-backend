<?php

namespace Modules\CRM\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\CRM\Models\Module;
use Modules\CRM\Models\Record;
use Modules\CRM\Models\RecordRelation;
use Modules\CRM\Models\RecordUserAssignment;
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
            ->with(['values.field','assignments.user'])
            ->get();

        return response()->json($records);
    }
    public function show(Record $record ,Request $request)
    {

         $record->load(['values.field','assignments.user']);

        return response()->json(['data'=>$record]);
    }

public function store(Request $request, Module $module)
{
    DB::beginTransaction();

    try {
        $record = Record::create([
            'module_id' => $module->id,
            'created_by' => auth()->id(),
            // 'record_id' => $request->parent_id,
            // 'relation_type' => $request->relation_type,
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

    public function getByRecord($recordId)
    {
        $data = RecordValue::with('field')
            ->where('record_id', $recordId)
            ->get();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'No record values found for this record ID'
            ], 400);
        }
        return response()->json([
            'status' => true,
            'record_id' => $recordId,
            'values' => $data
        ],200);
    }

    public function updateValue(Request $request, $id)
    {
        $recordValue = RecordValue::find($id);
        if (!$recordValue) {
            return response()->json([
                'message' => 'Record value not found'
            ], 400);
        }
        $recordValue->update([
            'value' => $request->value
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Value updated successfully',
            'data' => $recordValue
        ],200);
    }
    public function storeRecordValue(Request $request, $id)
    {
        $recordValue = RecordValue::find($id);
        if (!$recordValue) {
            return response()->json([
                'message' => 'Record value not found'
            ], 400);
        }
        $recordValue->update([
            'value' => $request->value
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Value updated successfully',
            'data' => $recordValue
        ],200);
    }

    public function addChild(Request $request){
        $request->validate([
            'parent_record_id' => 'required|exists:records,id',
            'child_record_id' => 'required|exists:records,id',
        ]);
        $parent = Record::where('id',$request->parent_record_id)->with('module')->first();
        $child = Record::where('id',$request->child_record_id)->with('module')->first();

        $relation_type = $parent->module->name.'-'.$child->module->name;
        RecordRelation::create([
            'parent_record_id' => $request->parent_record_id,
            'child_record_id' => $request->child_record_id,
            'relation_type' => $relation_type
        ]);
        return response()->json('Child Data added successfully');
    }
    public function getChild($record , $type){
        $childs = RecordRelation::where('parent_record_id',$record)->where('relation_type',$type)->pluck('child_record_id');

        $childData = Record::whereIn('id',$childs)->with('values.field','assignments.user')->get();

        return response()->json(['data'=>$childData,'relation_type'=>$type]);
    }

    public function assignRecord(Record $record, Request $request)
        {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'role' => 'required|string|max:50',
                'permission_level' => 'required|string|max:50',
            ]);
        $assignment = RecordUserAssignment::updateOrCreate(
            ['record_id' => $record->id, 'user_id' => $request->user_id],
            [
                'assigned_by' => Auth::id(),
                'role' => $request->role,
                'permission_level' => $request->permission_level,
                'assigned_at' => now(),
            ]
        );



            return response()->json([
                'message' => 'Record assigned successfully.',
                'data' => $assignment,
            ]);
        }

}
