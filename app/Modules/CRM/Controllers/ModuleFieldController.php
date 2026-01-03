<?php

namespace Modules\CRM\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
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
        $validator = Validator::make($request->all(), [
            'label' => 'sometimes|required|string|max:255',
            'order_group'=> 'sometimes|nullable|integer',
            'name' => 'sometimes|required|string|max:255|unique:module_fields,name,' . $field->id,
            'type' => 'sometimes|required|string|in:text,select,date,number,checkbox',
            'required' => 'sometimes|boolean',
            'unique' => 'sometimes|boolean',
            'options' => 'sometimes|array',
            'options.*' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if($request->has('options')){
            $options = $request->input('options');
            $validator->after(function ($validator) use ($options) {
                if (is_array($options) && count($options) === 0) {
                    $validator->errors()->add('options', 'The options field must have at least one option when provided.');
                }
            });
            if ($validator->fails()) {
                return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
        $field->update($validator->validated());

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
            ->orderBy('order')
            ->get();

        return response()->json([
            'message' => 'Module fields fetched successfully',
            'data' => $fields
        ]);
    }
}
