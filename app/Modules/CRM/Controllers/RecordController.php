<?php

namespace Modules\CRM\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\CRM\Models\CustomView;
use Modules\CRM\Models\Module;
use Modules\CRM\Models\ModuleField;
use Modules\CRM\Models\Record;
use Modules\CRM\Models\RecordRelation;
use Modules\CRM\Models\RecordUserAssignment;
use Modules\CRM\Models\RecordValue;
use App\Models\Customer;

class RecordController extends Controller
{

// public function index(Module $module)
// {
//     $query = $module->records()
//         ->with([ 'values' => function ($q) {
//         $q->join('module_fields', 'record_values.field_id', '=', 'module_fields.id')
//           ->orderBy('module_fields.order', 'asc')
//           ->select('record_values.*');
//     },'assignments.user']);

//     if (request()->custom_view_id) {
//     // Apply custom view filter
//     $viewId = request()->custom_view_id;
//   $view = CustomView::where('id',$viewId)->with('rootGroup.childrenRecursive.conditions')->first();

//     if ($view) {

//         $query = $this->applyCustomViewFilter($query, $view);
//     }
// } else {
//     if (request()->date_field && request()->start_date && request()->end_date) {

//         $dateFieldName = request()->date_field;

//         $query->whereHas('values', function ($q) use ($dateFieldName) {
//             $q->whereHas('field', fn($f) => $f->where('name', $dateFieldName))
//                 ->whereBetween('value', [
//                     request()->start_date,
//                     request()->end_date
//                 ]);
//         });
//     }
//     if (request()->field && request()->value) {

//         $fieldName = request()->field;
//         $fieldValue = request()->value;

//         $query->whereHas('values', function ($q) use ($fieldName, $fieldValue) {
//             $q->whereHas('field', fn($f) => $f->where('name', $fieldName))
//                 ->where('value', $fieldValue);
//         });
//     }
//     if (request()->has('filters') && is_array(request()->filters)) {

//     foreach (request()->filters as $fieldName => $fieldValue) {

//         $query->whereHas('values', function ($q) use ($fieldName, $fieldValue) {
//             $q->whereHas('field', fn($f) => $f->where('name', $fieldName));

//             is_array($fieldValue)
//                 ? $q->whereIn('value', $fieldValue)
//                 : $q->where('value', $fieldValue);
//         });
//     }
// }
//     if (request()->has('date_filters') && is_array(request()->date_filters)) {

//     foreach (request()->date_filters as $fieldName => $range) {

//         if (!isset($range['start']) || !isset($range['end'])) {
//             continue; // skip invalid ranges
//         }

//         $start = $range['start'];
//         $end   = $range['end'];

//         $query->whereHas('values', function ($q) use ($fieldName, $start, $end) {
//             $q->whereHas('field', fn($f) => $f->where('name', $fieldName))
//               ->whereBetween('value', [$start, $end]);
//         });
//     }
// }
// }

//     if (in_array(auth()->user()->role, ['sales-manager', 'sales-executive' ,'manager-cs','manager-sales','executive-cs','executive-sales'])) {
//         $mine = RecordUserAssignment::where('user_id', auth()->id())->pluck('record_id');
//         $query->whereIn('id', $mine);
//     }

//     if (request()->has('lite')) {
//         return response()->json([
//             'total' => $query->count()
//         ]);
//     }
//     if(request()->per_page)
//     {
//         $data = $query->paginate(request()->per_page);
//            return response()->json($data);
//     }
//     else
//         $data = $query->get();

//       return response()->json(['data'=>$data]);

// }

public function index(Module $module)
{
    $fieldsFilter = request()->has('fields')
    ? array_map('trim', explode(',', request()->fields))
    : null;
    $query = $module->records()
        // ->with([
        //     'values' => function ($q) {
        //         $q->join('module_fields', 'record_values.field_id', '=', 'module_fields.id')
        //           ->orderBy('module_fields.order', 'asc')
        //           ->select('record_values.*');
        //     },
        //     'assignments.user'
        // ]);
         ->with([
        'values' => function ($q) use ($fieldsFilter) {
            $q->join('module_fields', 'record_values.field_id', '=', 'module_fields.id')
              ->when($fieldsFilter, function ($qq) use ($fieldsFilter) {
                  $qq->whereIn('module_fields.name', $fieldsFilter);
              })
              ->orderBy('module_fields.order', 'asc')
              ->select('record_values.*');
        },
        'assignments.user'
    ]);

    // Sales / manager role restriction
    $salesRoles = ['sales-manager', 'sales-executive', 'manager-cs', 'manager-sales', 'executive-cs', 'executive-sales'];
    if (in_array(auth()->user()->role, $salesRoles)) {

        if ($module->name === 'Accounts') {
            // Accounts: only show assigned accounts
            $query->whereHas('assignments', fn($q) => $q->where('user_id', auth()->id()));
        } else {
            // Other modules: include only records related to Accounts assigned to current user
            $query->whereHas('relationsAsChild', function ($q) use ($module) {
                $q->whereHas('parent.assignments', fn($q2) => $q2->where('user_id', auth()->id()))
                  ->where('relation_type', 'Accounts-'.$module->name);
            });
        }
    }

    // Custom view filter
    if (request()->custom_view_id) {
        $viewId = request()->custom_view_id;
        $view = CustomView::where('id', $viewId)
            ->with('rootGroup.childrenRecursive.conditions')
            ->first();

        if ($view) {
            $query = $this->applyCustomViewFilter($query, $view);
        }
    } else {
        // Apply value-based filters
        if (request()->date_field && request()->start_date && request()->end_date) {
            $dateFieldName = request()->date_field;
            $query->whereHas('values', fn($q) =>
                $q->whereHas('field', fn($f) => $f->where('name', $dateFieldName))
                  ->whereBetween('value', [request()->start_date, request()->end_date])
            );
        }

            if (request()->field && request()->value) {
            $fieldName = request()->field;
            $fieldValue = request()->value;
            $query->whereHas('values', function ($q) use ($fieldName, $fieldValue) {
                $q->whereHas('field', fn($f) => $f->where('name', $fieldName));

                if (is_array($fieldValue)) {
                    $q->whereIn('value', $fieldValue);
                } else {
                    $q->where('value', $fieldValue);
                }
            });
        }

        $filters = $this->collectFiltersFromQuery();
        if (!empty($filters) && is_array($filters)) {
            foreach ($filters as $fieldName => $fieldValue) {
                $query->whereHas('values', function ($q) use ($fieldName, $fieldValue) {
                    $q->whereHas('field', fn($f) => $f->where('name', $fieldName));

                    if (is_array($fieldValue)) {
                        $q->whereIn('value', $fieldValue);
                    } else {
                        $q->where('value', $fieldValue);
                    }
                });
            }
        }


        if (request()->has('date_filters') && is_array(request()->date_filters)) {
            foreach (request()->date_filters as $fieldName => $range) {
                if (!isset($range['start']) || !isset($range['end'])) continue;
                $start = $range['start'];
                $end   = $range['end'];

                $query->whereHas('values', function ($q) use ($fieldName, $start, $end) {
                    $q->whereHas('field', fn($f) => $f->where('name', $fieldName))
                      ->whereBetween('value', [$start, $end]);
                });
            }
        }
    }

    // Lite mode: return only count
    if (request()->has('lite')) {
        return response()->json([
            'total' => $query->count()
        ]);
    }

    // Pagination
    if (request()->per_page) {
        $data = $query->paginate(request()->per_page);
        return response()->json($data);
    }

    // Return all results
    $data = $query->get();
    return response()->json(['data' => $data]);
}

/**
 * Collect filters from request input and raw query string.
 * Supports repeated `filters[field]=value` occurrences and comma-separated values.
 */
private function collectFiltersFromQuery()
{
    $filters = request()->input('filters', []);

    if (!is_array($filters)) {
        $filters = is_null($filters) ? [] : (array) $filters;
    }

    $queryString = request()->server('QUERY_STRING') ?? ($_SERVER['QUERY_STRING'] ?? '');
    if ($queryString) {
        preg_match_all('/filters\[(.*?)\]=([^&]*)/', $queryString, $matches, PREG_SET_ORDER);
        foreach ($matches as $m) {
            $key = rawurldecode($m[1]);
            $val = rawurldecode($m[2]);

            if ($key === '') continue;

            if (array_key_exists($key, $filters)) {
                // ensure existing entry is an array
                if (!is_array($filters[$key])) {
                    $filters[$key] = [$filters[$key]];
                }
                $filters[$key][] = $val;
            } else {
                $filters[$key] = $val;
            }
        }
    }

    // Normalize: convert comma-separated strings to arrays and trim/unique
    foreach ($filters as $k => $v) {
        if (is_array($v)) {
            $normalized = array_values(array_unique(array_map('trim', $v)));
            $filters[$k] = $normalized;
        } elseif (is_string($v) && strpos($v, ',') !== false) {
            $parts = array_map('trim', explode(',', $v));
            $filters[$k] = array_values(array_unique($parts));
        }
    }

    return $filters;
}


//    public function show(Module $module, $id)
// {
//     $record = Record::where('id', $id)
//         ->with([
//             'values' => function ($q) {
//                 $q->join('module_fields', 'record_values.field_id', '=', 'module_fields.id')
//                   ->orderBy('module_fields.order', 'asc')
//                   ->select('record_values.*'); // prevent column collision
//             },
//             'values.field',
//             'assignments.user'
//         ])
//         ->firstOrFail();
//       if (in_array(auth()->user()->role, ['sales-manager', 'sales-executive','crm-manager','crm-executive'])) {
//         try {
//     logActivity('viewed', $module->name, $id);
// } catch (\Exception $e) {
//     // optionally log error or skip silently
// }

//     }
//     return response()->json(['data' => $record]);
// }
public function show(Module $module, $id)
{
    $fieldsFilter = request()->has('fields')
        ? array_map('trim', explode(',', request()->fields))
        : null;

    $record = Record::where('id', $id)
        ->with([
            'values' => function ($q) use ($fieldsFilter) {
                $q->join('module_fields', 'record_values.field_id', '=', 'module_fields.id')
                  ->when($fieldsFilter, function ($qq) use ($fieldsFilter) {
                      $qq->whereIn('module_fields.name', $fieldsFilter);
                  })
                  ->orderBy('module_fields.order', 'asc')
                  ->select('record_values.*');
            },
            'values.field',
            'assignments.user'
        ])
        ->firstOrFail();

    if (in_array(auth()->user()->role, [
        'sales-manager', 'sales-executive', 'manager-cs', 'manager-sales', 'executive-cs', 'executive-sales'
    ])) {
        try {
            logActivity('viewed', $module->name, $id);
        } catch (\Exception $e) {
            // silent fail
        }
    }

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

    if (in_array(auth()->user()->role,  ['sales-manager', 'sales-executive','crm-manager','crm-executive'])) {
        logActivity(
            'created',
            $module->name,
            $record->id,
            $details = [
                'data' => $request->all()
            ]
        );
    }
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
       $oldValue = $recordValue->value;

            $recordValue->update([
                'value' => $request->value
            ]);
              if (in_array(auth()->user()->role,  ['sales-manager', 'sales-executive','crm-manager','crm-executive'])) {
            logActivity(
                'updated-field',
                'record-value',
                $recordValue->record_id,
                [
                    'field_id' => $recordValue->field_id,
                    'old_value' => $oldValue,
                    'new_value' => $request->value
                ]
            );
        }
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


             if (in_array(auth()->user()->role,  ['sales-manager', 'sales-executive','crm-manager','crm-executive'])) {
          logActivity(
                'updated-field',
                'record-value',
                $id,
                [
                    'field_id' => $request->field_id,
                    'new_value' => $request->value
                ]
            );
        }

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
         if (in_array(auth()->user()->role,  ['sales-manager', 'sales-executive','crm-manager','crm-executive'])) {
        logActivity(
            'added-child',
            $parent->module->name,
            $request->parent_record_id,
            [
                'child_record_id' => $request->child_record_id,
                'child_module' => $child->module->name,
                'relation_type' => $relation_type
            ]
        );
    }
        return response()->json('Child Data added successfully');
    }
  public function getChild($record, $type)
{
      $fieldsFilter = request()->has('fields')
        ? array_map('trim', explode(',', request()->fields))
        : null;
        if(request()->has('company_id'))
            {
                $recordIds = Customer::where('company_id',request()->company_id)->pluck('record_id');
                // dd($recordIds);
                  $childIds = RecordRelation::whereIn('parent_record_id', $recordIds)
        ->where('relation_type', $type)
        ->pluck('child_record_id');
            }
            else{
    $childIds = RecordRelation::where('parent_record_id', $record)
        ->where('relation_type', $type)
        ->pluck('child_record_id');
            }
    $query = Record::whereIn('id', $childIds);

   $query->with([
        'values' => function ($q) use ($fieldsFilter) {
            $q->join('module_fields', 'record_values.field_id', '=', 'module_fields.id')
              ->when($fieldsFilter, function ($qq) use ($fieldsFilter) {
                  $qq->whereIn('module_fields.name', $fieldsFilter);
              })
              ->orderBy('module_fields.order', 'asc')
              ->select('record_values.*');
        },
        'assignments.user'
    ]);

    // Sales / manager role restriction
    $salesRoles = ['sales-manager', 'sales-executive', 'manager-cs', 'manager-sales', 'executive-cs', 'executive-sales'];
    if (in_array(auth()->user()->role, $salesRoles)) {
       $query->whereHas('relationsAsChild', function ($q) {
        $q->whereHas('parent.assignments', function ($q2) {
            $q2->where('user_id', auth()->id());
            });
        });
    }

    // Custom view filter
    if (request()->custom_view_id) {
        $viewId = request()->custom_view_id;
        $view = CustomView::where('id', $viewId)
            ->with('rootGroup.childrenRecursive.conditions')
            ->first();

        if ($view) {
            $query = $this->applyCustomViewFilter($query, $view);
        }
    } else {
        // Apply value-based filters
        if (request()->date_field && request()->start_date && request()->end_date) {
            $dateFieldName = request()->date_field;
            $query->whereHas('values', fn($q) =>
                $q->whereHas('field', fn($f) => $f->where('name', $dateFieldName))
                  ->whereBetween('value', [request()->start_date, request()->end_date])
            );
        }

            if (request()->field && request()->value) {
            $fieldName = request()->field;
            $fieldValue = request()->value;
            $query->whereHas('values', function ($q) use ($fieldName, $fieldValue) {
                $q->whereHas('field', fn($f) => $f->where('name', $fieldName));

                if (is_array($fieldValue)) {
                    $q->whereIn('value', $fieldValue);
                } else {
                    $q->where('value', $fieldValue);
                }
            });
        }

        $filters = $this->collectFiltersFromQuery();
        if (!empty($filters) && is_array($filters)) {
            foreach ($filters as $fieldName => $fieldValue) {
                $query->whereHas('values', function ($q) use ($fieldName, $fieldValue) {
                    $q->whereHas('field', fn($f) => $f->where('name', $fieldName));

                    if (is_array($fieldValue)) {
                        $q->whereIn('value', $fieldValue);
                    } else {
                        $q->where('value', $fieldValue);
                    }
                });
            }
        }


        if (request()->has('date_filters') && is_array(request()->date_filters)) {
            foreach (request()->date_filters as $fieldName => $range) {
                if (!isset($range['start']) || !isset($range['end'])) continue;
                $start = $range['start'];
                $end   = $range['end'];

                $query->whereHas('values', function ($q) use ($fieldName, $start, $end) {
                    $q->whereHas('field', fn($f) => $f->where('name', $fieldName))
                      ->whereBetween('value', [$start, $end]);
                });
            }
        }
    }

    // Lite mode: return only count
    if (request()->has('lite')) {
        return response()->json([
            'total' => $query->count()
        ]);
    }

    // Pagination
    if (request()->per_page) {
        $data = $query->paginate(request()->per_page);
        return response()->json($data);
    }

    // Return all results
    $data = $query->get();
    return response()->json(['data' => $data]);
}


   public function assignRecord(Record $record, Request $request)
{
    // If bulk assigning
    if ($request->has('assignments')) {

        $request->validate([
            'assignments' => 'required|array|min:1',
            'assignments.*.user_id' => 'required|exists:users,id',
            'assignments.*.role' => 'required|string|max:50',
            'assignments.*.permission_level' => 'required|string|max:50',
        ]);

        $results = [];

        foreach ($request->assignments as $assign) {

            $userId = $assign['user_id'];
            $role = $assign['role'];
            $permission = $assign['permission_level'];

            // Check if this role already exists for this record
            $existingRole = RecordUserAssignment::where('record_id', $record->id)
                ->where('role', $role)
                ->first();

            if ($existingRole) {
                // Replace user for existing role
                $existingRole->update([
                    'user_id' => $userId,
                    'permission_level' => $permission,
                    'assigned_by' => Auth::id(),
                    'assigned_at' => now(),
                ]);

                $results[] = [
                    'action' => 'updated',
                    'role' => $role,
                    'data' => $existingRole
                ];
                continue;
            }

            // Create new assignment
            $newAssignment = RecordUserAssignment::create([
                'record_id' => $record->id,
                'user_id' => $userId,
                'role' => $role,
                'permission_level' => $permission,
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
            ]);

            $results[] = [
                'action' => 'created',
                'role' => $role,
                'data' => $newAssignment
            ];
        }

        return response()->json([
            'message' => 'Bulk assignment processed successfully.',
            'results' => $results,
        ]);
    }

    // ---------------------------------------------------------------------
    // SINGLE ASSIGNMENT (existing frontend — untouched)
    // ---------------------------------------------------------------------

    $request->validate([
        'user_id' => 'required|exists:users,id',
        'role' => 'required|string|max:50',
        'permission_level' => 'required|string|max:50',
    ]);

    // Check if this role exists already
    $existingRole = RecordUserAssignment::where('record_id', $record->id)
        ->where('role', $request->role)
        ->first();

    if ($existingRole) {
        // Replace user for that role
        $existingRole->update([
            'user_id' => $request->user_id,
            'permission_level' => $request->permission_level,
            'assigned_by' => Auth::id(),
            'assigned_at' => now(),
        ]);

        return response()->json([
            'message' => 'Role reassigned successfully.',
            'data' => $existingRole,
        ]);
    }

    // Create normal assignment
    $assignment = RecordUserAssignment::create([
        'record_id' => $record->id,
        'user_id' => $request->user_id,
        'role' => $request->role,
        'permission_level' => $request->permission_level,
        'assigned_by' => Auth::id(),
        'assigned_at' => now(),
    ]);

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
          if (in_array(auth()->user()->role,  ['sales-manager', 'sales-executive','crm-manager','crm-executive'])) {
        logActivity('deleted', $record->module->name, $record->id);
    }
        return response()->json([
            'message' => 'Record deleted successfully'
        ]);
    }
   public function convertDealToProject($dealId)
{
    DB::beginTransaction();

    try {
        // 1. Create the project
        $project =  Record::create([
            'module_id' => 7,
            'created_by' => auth()->id(),
            // 'record_id' => $request->parent_id,
            // 'relation_type' => $request->relation_type,
        ]);

        // 2. Copy dynamic fields
        // Fetch project module fields
        $projectFields = ModuleField::where('module_id', 7)->get()->keyBy('name');
        $deal =Record::where('id',$dealId)->with('values.field')->first();
        foreach ($deal->values as $dealValue) {
            // Try to find a matching project field by name (or key)
            if (isset($projectFields[$dealValue->field->name])) {
                $field = $projectFields[$dealValue->field->name];

                $project->values()->create([
                    'field_id' => $field->id,
                    'value' => $dealValue->value,
                ]);
            }
        }
        $relations = RecordRelation::where('parent_record_id',$dealId)->with('child.module')->get();
        $relations2 = RecordRelation::where('child_record_id',$dealId)->with('parent.module')->get();
        foreach($relations as $relation)
        {
            RecordRelation::create([
                'parent_record_id' => $project->id,
                'child_record_id' => $relation->child_record_id,
                'relation_type' =>  'Projects'.'-'.$relation->child->module->name
            ]);
        }
        foreach($relations2 as $relation)
        {
            RecordRelation::create([
                'child_record_id' => $project->id,
                'parent_record_id' => $relation->parent_record_id,
                'relation_type' => $relation->parent->module->name .'-'.'Projects'
            ]);
        }
        DB::commit();

        return $project;
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}
public function assignRoleToMultipleRecords(Request $request)
{
    $request->validate([
        'record_ids' => 'required|array|min:1',
        'record_ids.*' => 'required|exists:records,id',
        'user_id' => 'required|exists:users,id',
        'role' => 'required|string|max:50',
        'permission_level' => 'required|string|max:50',
    ]);

    $userId = $request->user_id;
    $role = $request->role;
    $permission = $request->permission_level;

    $results = [];

    foreach ($request->record_ids as $recordId) {

        // Check if the role already exists for this record
        $existingRoleAssignment = RecordUserAssignment::where('record_id', $recordId)
            ->where('role', $role)
            ->first();

        if ($existingRoleAssignment) {

            // Update (replace user)
            $existingRoleAssignment->update([
                'user_id' => $userId,
                'permission_level' => $permission,
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
            ]);

            $results[] = [
                'record_id' => $recordId,
                'action' => 'updated',
                'data' => $existingRoleAssignment
            ];

            continue;
        }

        // Otherwise create new assignment
        $newAssignment = RecordUserAssignment::create([
            'record_id' => $recordId,
            'user_id' => $userId,
            'role' => $role,
            'permission_level' => $permission,
            'assigned_by' => Auth::id(),
            'assigned_at' => now(),
        ]);

        $results[] = [
            'record_id' => $recordId,
            'action' => 'created',
            'data' => $newAssignment
        ];
    }

    return response()->json([
        'message' => 'Multiple record assignments processed successfully.',
        'results' => $results
    ]);
}
public function applyCustomViewFilter($query,CustomView $view)
{
    return $this->applyGroup($query, $view->rootGroup);
}
private function applyGroup($query, $group)
{
    return $query->where(function ($q) use ($group) {

        foreach ($group->conditions as $condition) {
            $this->applyCondition($q, $condition, $group->join_type);
        }

        foreach ($group->children as $child) {
            $q->{$group->join_type === 'AND' ? 'where' : 'orWhere'}(function ($nested) use ($child) {
                $this->applyGroup($nested, $child);
            });
        }

    });
}
private function applyCondition($query, $cond, $join)
{
    $method = $join === 'AND' ? 'whereHas' : 'orWhereHas';

    $query->$method('values', function ($q) use ($cond) {
        // Match the correct field by ID
        $q->where('field_id', $cond->field);

        // Apply operator
        switch ($cond->operator) {
            case 'contains':
                $q->where('value', 'like', '%' . $cond->value . '%');
                break;

            case 'does_not_contain':
                $q->where('value', 'not like', '%' . $cond->value . '%');
                break;

            case 'is':
                $q->where('value', $cond->value);
                break;

            case 'between':
                $q->whereBetween('value', $cond->value);
                break;

            // Add more operators as needed
        }
    });
}

public function globalSearch(Request $request)
{
    $search = trim($request->search);

    $modules = Module::with(['records' => function ($query) use ($search) {

        // reuse same index logic via a trait (recommended)
        $this->applyRecordBaseQuery($query);

        $query->whereHas('values', function ($q) use ($search) {
            $q->where('value', 'LIKE', "%{$search}%");
        })->limit(10);

    }])->get();

    return response()->json(
        $modules->mapWithKeys(fn ($m) => [
            $m->name => $m->records
        ])
    );
}

}
