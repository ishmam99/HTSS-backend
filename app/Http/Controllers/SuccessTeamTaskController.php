<?php

namespace App\Http\Controllers;

use App\Models\SuccessTeamTask;
use App\Models\SuccessTeamTaskOutput;
use Illuminate\Http\Request;

class SuccessTeamTaskController extends Controller
{
       /**
     * List all tasks
     */
    public function index(Request $request)
    {
         $query = SuccessTeamTask::advancedQuery($request);
        $lists = $request->per_page
        ? $query->paginate($request->per_page)
        : $query->get();

    return response()->json([
            'success' => true,
            'data' => $lists,
             'total' => SuccessTeamTask::count()
        ]);
    }

    /**
     * Create task ONLY
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'success_team_id' => 'required|exists:success_teams,id',
            'solution_id'     => 'required|exists:solutions,id',
            'software_id'     => 'nullable|exists:softwares,id',
            'assigned_to'     => 'nullable|exists:users,id',
            'description'     => 'required|string',
            'type'            => 'required|string',
            'date'             => 'required|date'
        ]);

        $task = SuccessTeamTask::create([
            ...$data,
            'user_id' => auth()->id(),
        ]);

        return response()->json($task, 201);
    }

    /**
     * Show single task with outputs
     */
    public function show($id)
    {
        return SuccessTeamTask::with('solution','outputs','user','successTeam','assignedPerson','software')->findOrFail($id);
    }

    /**
     * Update task
     */
    public function update(Request $request, $id)
    {
        $task = SuccessTeamTask::findOrFail($id);

        $data = $request->validate([
            'assigned_to'  => 'sometimes|exists:users,id',
            'description'  => 'sometimes|string',
            'status'       => 'sometimes|string',
            'type'       => 'sometimes|string',
            'completed_at' => 'nullable|date',
            'date' => 'nullable|date',
        ]);

        // Auto set completed_at if status becomes completed
        if (($data['status'] ?? null) === 'completed') {
            $data['completed_at'] = now();
        }

        $task->update($data);

        return response()->json($task);
    }

    /**
     * Delete task (outputs cascade)
     */
    public function destroy($id)
    {
        SuccessTeamTask::findOrFail($id)->delete();

        return response()->json(['message' => 'Task deleted']);
    }

    /**
     * -------------------------
     * OUTPUT CRUD (SEPARATE)
     * -------------------------
     */

    /**
     * Add output to task
     */
    public function storeOutput(Request $request, $taskId)
    {
        $request->validate([
            'output' => 'required|string',
            'date'  => 'required|date',

        ]);

        $task = SuccessTeamTask::findOrFail($taskId);

        $output = $task->outputs()->create([
            'output' => $request->output,
            'date' => $request->date,
            'status' => 0,
        ]);

        return response()->json($output, 201);
    }

    /**
     * Update output
     */
    public function updateOutput(Request $request, $id)
    {
        $data = $request->validate([
            'output' => 'nullable|string',
            'status' => 'nullable|integer',
            'completed_at' => 'nullable|date'
        ]);

        $output = SuccessTeamTaskOutput::findOrFail($id);
        $output->update($data);

        return response()->json($output);
    }

    /**
     * Delete output
     */
    public function deleteOutput($id)
    {
        SuccessTeamTaskOutput::findOrFail($id)->delete();

        return response()->json(['message' => 'Output deleted']);
    }
    public function myOutputs(Request $request)
    {
        $myTaskIds = SuccessTeamTask::where('assigned_to',auth()->id())->pluck('id');
        $queryes = SuccessTeamTaskOutput::advancedQuery($request);
            $query = $queryes->whereIn('success_team_task_id',$myTaskIds);
             $lists = $request->per_page
        ? $query->paginate($request->per_page)
        : $query->get();

    return response()->json([
            'success' => true,
            'data' => $lists,
             'total' => $query->count()
        ]);
    }
    public function teamOutputs(Request $request,$id)
    {
        $myTaskIds = SuccessTeamTask::where('success_team_id',$id)->pluck('id');
        $queryes = SuccessTeamTaskOutput::advancedQuery($request);
            $query = $queryes->whereIn('success_team_task_id',$myTaskIds);
             $lists = $request->per_page
        ? $query->paginate($request->per_page)
        : $query->get();

    return response()->json([
            'success' => true,
            'data' => $lists,
             'total' => $query->count()
        ]);
    }


}
