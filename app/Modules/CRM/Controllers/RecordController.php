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
    // public function index(Module $module)
    // {

    //       $records = $module->records()
    //         ->with(['values.field','assignments.user'])
    //         ->get();
    //     $myRecord = RecordUserAssignment::where('user_id',auth()->id())->pluck('record_id');
    //     if(auth()->user()->role == 'sales-manager' || auth()->user()->role == 'sales-executive')
    //     {
    //         $records = $records->whereIn('id', $myRecord);
    //     }
    //     // return response()->json($result);
    //     return response()->json($records);
    // }
public function index(Module $module)
{
    $query = $module->records()
        ->with([ 'values' => function ($q) {
        $q->join('module_fields', 'record_values.field_id', '=', 'module_fields.id')
          ->orderBy('module_fields.order', 'asc')
          ->select('record_values.*');
    },'assignments.user']);

    if (request()->date_field && request()->start_date && request()->end_date) {

        $dateFieldName = request()->date_field;

        $query->whereHas('values', function ($q) use ($dateFieldName) {
            $q->whereHas('field', fn($f) => $f->where('name', $dateFieldName))
                ->whereBetween('value', [
                    request()->start_date,
                    request()->end_date
                ]);
        });
    }
    if (request()->field && request()->value) {

        $fieldName = request()->field;
        $fieldValue = request()->value;

        $query->whereHas('values', function ($q) use ($fieldName, $fieldValue) {
            $q->whereHas('field', fn($f) => $f->where('name', $fieldName))
                ->where('value', $fieldValue);
        });
    }
    if (request()->has('filters') && is_array(request()->filters)) {

    foreach (request()->filters as $fieldName => $fieldValue) {

        $query->whereHas('values', function ($q) use ($fieldName, $fieldValue) {
            $q->whereHas('field', fn($f) => $f->where('name', $fieldName));

            is_array($fieldValue)
                ? $q->whereIn('value', $fieldValue)
                : $q->where('value', $fieldValue);
        });
    }
}
    if (request()->has('date_filters') && is_array(request()->date_filters)) {

    foreach (request()->date_filters as $fieldName => $range) {

        if (!isset($range['start']) || !isset($range['end'])) {
            continue; // skip invalid ranges
        }

        $start = $range['start'];
        $end   = $range['end'];

        $query->whereHas('values', function ($q) use ($fieldName, $start, $end) {
            $q->whereHas('field', fn($f) => $f->where('name', $fieldName))
              ->whereBetween('value', [$start, $end]);
        });
    }
}


    if (in_array(auth()->user()->role, ['sales-manager', 'sales-executive'])) {
        $mine = RecordUserAssignment::where('user_id', auth()->id())->pluck('record_id');
        $query->whereIn('id', $mine);
    }

    if (request()->has('lite')) {
        return response()->json([
            'total' => $query->count()
        ]);
    }
    if(request()->per_page)
    {
        $data = $query->paginate(request()->per_page);
           return response()->json($data);
    }
    else
        $data = $query->get();

      return response()->json(['data'=>$data]);

}



   public function show(Module $module, $id)
{
    $record = Record::where('id', $id)
        ->with([
            'values' => function ($q) {
                $q->join('module_fields', 'record_values.field_id', '=', 'module_fields.id')
                  ->orderBy('module_fields.order', 'asc')
                  ->select('record_values.*'); // prevent column collision
            },
            'values.field',
            'assignments.user'
        ])
        ->firstOrFail();

    return response()->json(['data' => $record]);
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
        $record = Record::where('id',$recordId)->with('assignments.user')->first();
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
                'assignments' => $record->assignments,
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
         $recordValue = RecordValue::updateOrCreate(
            ['record_id' => $id, 'field_id' => $request->field_id],
            ['value' => $request->value]);

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

    public function updateRecordAssignment($id, Request $request)
    {
        $assignment = RecordUserAssignment::find($id);
        if (!$assignment) {
            return response()->json([
                'message' => 'Assignment not found'
            ], 400);
        }

        $assignment->update([
            'user_id' => $request->user_id ?? $assignment->user_id,
            'permission_level' => $request->permission_level ?? $assignment->permission_level,
        ]);
        return response()->json([
            'message' => 'Assignment updated successfully',
            'data' => $assignment
        ]);
    }

    public function destroy(Record $record)
    {
        $record->delete();
        return response()->json([
            'message' => 'Record deleted successfully'
        ]);
    }

}
