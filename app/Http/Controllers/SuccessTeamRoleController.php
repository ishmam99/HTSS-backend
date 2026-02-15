<?php

namespace App\Http\Controllers;

use App\Models\SuccessTeamRole;
use Illuminate\Http\Request;

class SuccessTeamRoleController extends Controller
{
      public function index(Request $request)
    {
        // Get all software with related skill
        $query = SuccessTeamRole::advancedQuery($request);
        $lists = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();
        return response()->json([
            'success' => true,
            'data' => $lists,
             'total' => SuccessTeamRole::count()
        ]);
    }

    /**
     * Store new role
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|max:255',
        ]);

        $role = SuccessTeamRole::create($validated);

        return response()->json([
            'message' => 'Role created successfully',
           
        ], 201);
    }

    /**
     * Show single role
     */
    public function show(SuccessTeamRole $successTeamRole)
    {
        return response()->json(
            $successTeamRole->load('user')
        );
    }

    /**
     * Update role
     */
    public function update(Request $request, SuccessTeamRole $successTeamRole)
    {
        $validated = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'role' => 'sometimes|string|max:255',
        ]);

        $successTeamRole->update($validated);

        return response()->json([
            'message' => 'Role updated successfully',
            'data' => $successTeamRole->load('user')
        ]);
    }

    /**
     * Delete role
     */
    public function destroy(SuccessTeamRole $successTeamRole)
    {
        $successTeamRole->delete();

        return response()->json([
            'message' => 'Role deleted successfully'
        ]);
    }
}
