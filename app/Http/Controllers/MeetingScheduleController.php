<?php
namespace App\Http\Controllers;

use App\Http\Requests\MeetingScheduleRequest;
use App\Models\MeetingSchedule;
use Illuminate\Http\Request;

class MeetingScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = MeetingSchedule::advancedQuery($request);
        $query->with(['users', 'successTeam', 'createdBy']);

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

    // public function update(MeetingScheduleRequest $request, $id)
    // {
    //     $meetingSchedule = MeetingSchedule::findOrFail($id);
    //     $meetingSchedule->update($request->validated());
    //     if (! empty($request->success_team_user_ids)) {
    //         $meetingSchedule->users()->sync($request->success_team_user_ids);
    //     }
    //     return response()->json([
    //         'success' => true,
    //         'data'    => $meetingSchedule->load('users'),
    //     ]);
    // }

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
