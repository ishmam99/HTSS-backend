<?php

namespace Modules\CRM\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\CRM\Models\ModuleField;

class ModuleFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $fields = ModuleField::with('module')->get();

        return response()->json($fields);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'module_id' => 'required|integer|exists:modules,id',
            'label' => 'required|string|max:255',
            'order_group' => 'nullable|integer',
             'name' => [
            'required',
            'string',
            'max:255',
            Rule::unique('module_fields')->where(function ($query) use ($request) {
                return $query->where('module_id', $request->module_id);
            }),
        ],
            'type' => 'required|string|in:text,select,date,number,checkbox',
            'required' => 'nullable',
            'unique' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $field = ModuleField::create($validator->validated());

        return response()->json($field, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(ModuleField $field): JsonResponse
    {
        // The 'field' parameter name matches the apiResource name in your routes
        return response()->json($field->load('module'));
    }

    /**
     * Update the specified resource in storage.
     */
 public function update(Request $request, ModuleField $field): JsonResponse
{
    $rules = [
        'label' => 'required|string|max:255',
        'order_group'=> 'nullable|integer',
        'type' => 'required|string|in:text,select,date,number,checkbox',
        'required' => 'boolean',
        'unique' => 'boolean',
        'options' => 'sometimes|array|min:1',
        'options.*' => 'string',
    ];

    // Only validate unique if name is changed
    if ($request->name !== $field->name) {
        $rules['name'] = [
            'required',
            'string',
            'max:255',
            Rule::unique('module_fields', 'name'),
        ];
    } else {
        $rules['name'] = 'required|string|max:255';
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $oldOptions = $field->options ?? [];
    $newOptions = $request->input('options', []);

    // Detect renamed options
    $mapping = [];
    foreach ($oldOptions as $i => $old) {
        if (isset($newOptions[$i]) && $newOptions[$i] !== $old) {
            $mapping[$old] = $newOptions[$i];
        }
    }

    $field->update($validator->validated());

    // Update record_values
    foreach ($mapping as $old => $new) {
        DB::table('record_values')
            ->where('field_id', $field->id)
            ->where('value', $old)
            ->update(['value' => $new]);
    }

    return response()->json($field);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ModuleField $field): JsonResponse
    {
        $field->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
   public function getByModule($moduleId)
{
    $fields = ModuleField::where('module_id', $moduleId)
        ->orderByRaw('`order` IS NULL') // NULLs last
        ->orderBy('order')              // then actual order
        ->get();

    return response()->json([
        'message' => 'Module fields fetched successfully',
        'data' => $fields
    ]);
}

}
