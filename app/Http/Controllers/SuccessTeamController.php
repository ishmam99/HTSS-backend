<?php

namespace App\Http\Controllers;

use App\Models\SuccessTeam;
use Illuminate\Http\Request;

class SuccessTeamController extends Controller
{
    // List all teams with members & companies, paginated
    public function index()
    {
        return SuccessTeam::with(['members', 'companies', 'owner'])->paginate(10);
    }

    // Create new team
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'status' => 'nullable|in:0,1',
            'company_id' => 'nullable|exists:companies,id',
            'members' => 'nullable|array',
            'members.*.id' => 'required|exists:users,id',
            'members.*.role' => 'required|string|max:255',
            'companies' => 'nullable|array',
            'companies.*' => 'exists:companies,id',
        ]);

        $team = SuccessTeam::create($request->only('name', 'user_id', 'status', 'company_id'));

        // Assign members
        if ($request->has('members')) {
            $members = [];
            foreach ($request->members as $member) {
                $members[$member['id']] = ['role' => $member['role']];
            }
            $team->members()->sync($members);
        }

        // Assign companies
        if ($request->has('companies')) {
            $team->companies()->sync($request->companies);
        }

        return response()->json($team->load(['members', 'companies', 'owner']));
    }

    // Show single team
    public function show($id)
    {
        $team = SuccessTeam::with(['members', 'companies', 'owner'])->findOrFail($id);
        return response()->json($team);
    }

    // Update team info (name, owner, status, company_id)
    public function update(Request $request, $id)
    {
        $team = SuccessTeam::findOrFail($id);

        $request->validate([
            'name' => 'nullable|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'status' => 'nullable|in:0,1',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $team->update($request->only('name', 'user_id', 'status', 'company_id'));

        return response()->json($team->load(['members', 'companies', 'owner']));
    }

    // Delete team
    public function destroy($id)
    {
        $team = SuccessTeam::findOrFail($id);
        $team->delete();

        return response()->json(['message' => 'SuccessTeam deleted']);
    }

    // Assign members and companies to an existing team
    public function assign(Request $request, $id)
    {
        $team = SuccessTeam::findOrFail($id);

        $request->validate([
            'members' => 'nullable|array',
            'members.*.id' => 'required|exists:users,id',
            'members.*.role' => 'required|string|max:255',
            'companies' => 'nullable|array',
            'companies.*' => 'exists:companies,id',
        ]);

        // Assign members
        if ($request->has('members')) {
            $members = [];
            foreach ($request->members as $member) {
                $members[$member['id']] = ['role' => $member['role']];
            }
            $team->members()->sync($members);
        }

        // Assign companies
        if ($request->has('companies')) {
            $team->companies()->sync($request->companies);
        }

        return response()->json($team->load(['members', 'companies', 'owner']));
    }

}
