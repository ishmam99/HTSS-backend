<?php
namespace App\Http\Controllers;

use App\Http\Requests\MonthlyCSMActivityRequest;
use App\Http\Resources\MonthlyCSMActivityResource;
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
      

        return response()->json([
            'success' => true,
            'data'    => $lists,
        ]);
    }

    public function activityByUser(Request $request)
    {
        $reports = MonthlyCSMActivity::with(['user', 'customer.user'])
            ->when(
                $request->filled('customer_id'),
                fn($q) =>
                $q->where('customer_id', $request->customer_id)
            )
            ->when(
                $request->filled(['start_date', 'end_date']),
                fn($q) =>
                $q->whereBetween('date', [
                    $request->start_date,
                    $request->end_date,
                ])
            )
            ->when(
                $request->filled('start_date') && ! $request->filled('end_date'),
                fn($q) =>
                $q->whereDate('date', '>=', $request->start_date)
            )

            ->when(
                $request->filled('end_date') && ! $request->filled('start_date'),
                fn($q) =>
                $q->whereDate('date', '<=', $request->end_date)
            )
            ->orderBy('date', 'desc')
            ->get();

        return MonthlyCSMActivityResource::collection($reports);
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

    public function getCompanyCSMReports(Request $request, $company_id)
    {
        $reports = MonthlyCSMActivity::with(['user', 'customer.user'])
            ->whereHas('customer', function ($q) use ($company_id) {
                $q->where('company_id', $company_id);
            })
            ->when(
                $request->filled(['start_date', 'end_date']),
                fn($q) =>
                $q->whereBetween('date', [
                    $request->start_date,
                    $request->end_date,
                ])
            )
            ->when(
                $request->filled('start_date') && ! $request->filled('end_date'),
                fn($q) =>
                $q->whereDate('date', '>=', $request->start_date)
            )

            ->when(
                $request->filled('end_date') && ! $request->filled('start_date'),
                fn($q) =>
                $q->whereDate('date', '<=', $request->end_date)
            )
            ->orderBy('date', 'desc')
            ->get();

        return MonthlyCSMActivityResource::collection($reports);
    }
}
