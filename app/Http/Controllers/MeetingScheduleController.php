<?php
namespace App\Http\Controllers;

use App\Http\Requests\MeetingScheduleRequest;
use App\Models\MeetingSchedule;
use Illuminate\Http\Request;

class MeetingScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = MeetingSchedule::with(['users', 'successTeam', 'createdBy']);
        $query->when($request->created_by, function ($q) use ($request) {
            $q->where('created_by', $request->created_by);
        });
        $query->when($request->success_team_id, function ($q) use ($request) {
            $q->where('success_team_id', $request->success_team_id);
        });
        $query->when($request->start_date && $request->end_date, function ($q) use ($request) {
            $q->whereBetween('date', [$request->start_date, $request->end_date]);
        });

        $lists = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();
        return response()->json([
            'success' => true,
            'data'    => $lists,
        ]);
    }

    public function store(MeetingScheduleRequest $request)
    {
        $data               = $request->validated();
        $data['created_by'] = auth()->id();
        $successTeamUserIds = $data['success_team_user_id'];
        unset($data['success_team_user_id']);
        $meetingSchedule = MeetingSchedule::create($data);
        if (! empty($successTeamUserIds)) {
            $meetingSchedule->users()->attach($successTeamUserIds);
        }
        return response()->json([
            'success' => true,
            'data'    => $meetingSchedule->load('users'),
        ]);
    }

    public function show($id)
    {

    }

    public function update(MeetingScheduleRequest $request, $id)
    {
        $meetingSchedule    = MeetingSchedule::findOrFail($id);
        $data               = $request->validated();
        $data['created_by'] = auth()->id();
        $successTeamUserIds = $data['success_team_user_id'];
        unset($data['success_team_user_id']);
        $meetingSchedule->update($data);
        if (! empty($successTeamUserIds)) {
            $meetingSchedule->users()->sync($successTeamUserIds);
        }
        return response()->json([
            'success' => true,
            'data'    => $meetingSchedule->load('users'),
        ]);
    }

    public function destroy($id)
    {
        $meetingSchedule = MeetingSchedule::findOrFail($id);
        $meetingSchedule->delete();
        return response()->json([
            'success' => true,
            'message' => 'Meeting schedule deleted successfully',
        ]);
    }
}
