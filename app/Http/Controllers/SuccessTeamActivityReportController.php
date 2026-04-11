<?php
namespace App\Http\Controllers;

use App\Http\Requests\SuccessTeamActivityReportRequest;
use App\Models\SuccessTeamActivityReport;
use App\Models\SuccessTeamTaskOutput;
use Illuminate\Http\Request;

class SuccessTeamActivityReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = SuccessTeamActivityReport::with(['user', 'successTeam'])
            ->when($request->success_team_id, fn($q) =>
                $q->where('success_team_id', $request->success_team_id)
            )
            ->when($request->period, fn($q) =>
                $q->where('period', $request->period)
            )
            ->when($request->filled('status'), fn($q) =>
                $q->where('status', $request->status)
            )
            ->latest()
            ->paginate(15);

        return response()->json($reports);
    }

    /**
     * POST /api/activity-reports
     */
    public function store(SuccessTeamActivityReportRequest $request)
    {
        // prevent duplicate report per team + month
        $exists = SuccessTeamActivityReport::where('success_team_id', $request->success_team_id)
            ->where('period', $request->period)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Activity report already exists for this team and period',
            ], 422);
        }

        $report = SuccessTeamActivityReport::create([
            'user_id'            => auth()->user()->id,
            'success_team_id'    => $request->success_team_id,
            'period'             => $request->period,
            'status'             => $request->status ?? 0,
            'summary_activities' => $request->summary_activities,
            'key_outcomes'       => $request->key_outcomes,
        ]);

        return response()->json($report, 201);
    }

    /**
     * GET /api/activity-reports/{id}
     */
    public function show($id)
    {
        $activityReport = SuccessTeamActivityReport::find($id);

        return response()->json([
            'report'  => $activityReport->load(['user', 'successTeam']),
            'outputs' => $activityReport->taskOutputs()->get(),
        ]);
    }

    /**
     * PUT /api/activity-reports/{id}
     */
    public function update(
        Request $request,
        $id
    ) {
        $activityReport = SuccessTeamActivityReport::findOrFail($id);
        $activityReport->update(['status' => $request->status]);

        return response()->json($activityReport);
    }

    /**
     * DELETE /api/activity-reports/{id}
     */
    public function destroy(SuccessTeamActivityReport $success_team_activity_report)
    {
        $success_team_activity_report->delete();

        return response()->json([
            'message' => 'Activity report deleted successfully',
        ]);
    }
    public function getSuccessTeamReports(Request $request, $company_id)
    {
        $reports = SuccessTeamActivityReport::whereHas('successTeam', function ($q) use ($company_id) {
            $q->where('company_id', $company_id);
        })->with(['user', 'successTeam'])
            ->when($request->period, fn($q) =>
                $q->where('period', $request->period)
            )
            ->when($request->filled('status'), fn($q) =>
                $q->where('status', $request->status)
            )
            ->latest()
            ->paginate(15);

        return response()->json($reports);
    }

        public function getTaskOutputsByTeamAndDateRange(Request $request, $team_id)
        {
            $outputs = SuccessTeamTaskOutput::whereHas('successTeamTask.successTeam', function ($q) use ($team_id) {
                $q->where('success_team_id', $team_id);
            })
          
            ->when($request->filled('start_date') && $request->filled('end_date'), fn($q) =>
                $q->whereBetween('completed_at', [$request->start_date, $request->end_date])
            )
            ->with('successTeamTask')
            ->get()
            ->groupBy('success_team_task_id');

            return response()->json($outputs);
        }
    
}
