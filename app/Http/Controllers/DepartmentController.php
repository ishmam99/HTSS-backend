<?php

namespace App\Http\Controllers;

use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::advancedQuery($request);

        $lists = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();

        return response()->json([
            'success' => true,
            'data' => DepartmentResource::collection($lists),
            'total' => Department::count(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_department_id' => 'nullable|exists:departments,id',
            'status' => 'nullable|integer',
        ]);

        $department = Department::create($validated);

        return response()->json([
            'message' => 'Department created successfully',
            'data' => new DepartmentResource($department),
        ], 201);
    }

    public function show(Department $department)
    {
        return new DepartmentResource($department);
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'status' => 'sometimes|integer',
            'parent_department_id' => [
                'sometimes',
                'nullable',
                'exists:departments,id',
            ],
        ]);

        $department->update($validated);

        return response()->json([
            'message' => 'Department updated successfully',
            'data' => new DepartmentResource($department->fresh()),
        ]);
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return response()->json([
            'message' => 'Department deleted successfully',
        ]);
    }

    public function active(Request $request)
    {
        $query = Department::orderBy('id', 'desc');

        if ($request->filled('per_page')) {
            $positions = $query->paginate((int) $request->per_page);
        } else {
            $positions = $query->get();
        }

        return DepartmentResource::collection($positions);

    }
}
