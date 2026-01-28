<?php
namespace App\Http\Controllers;

use App\Http\Requests\MonthlyCSMActivityRequest;
use App\Models\MonthlyCSMActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MonthlyCSMActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = MonthlyCSMActivity::advancedQuery($request);
        $lists = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();
        $lists = MonthlyCSMActivity::all();

        return response()->json([
            'success' => true,
            'data'    => $lists,
        ]);
    }

    public function store(MonthlyCSMActivityRequest $request)
    {
        $data            = $request->validated();
        $data['user_id'] = auth()->user()->id;
        $data['date']    = Carbon::parse($request->date);
        MonthlyCSMActivity::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Monthly CSM Activity created successfully',
        ], 201);
    }

    public function update(MonthlyCSMActivityRequest $request, MonthlyCSMActivity $monthly_csm_activity)
    {
        $data            = $request->validated();
        $data['user_id'] = auth()->user()->id;
        $data['date']    = Carbon::parse($request->date);
        $monthly_csm_activity->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'Monthly CSM Activity updated successfully',
        ]);
    }

    public function destroy(MonthlyCSMActivity $monthly_csm_activity)
    {
        $monthly_csm_activity->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Monthly CSM Activity deleted successfully',
        ]);
    }
}
