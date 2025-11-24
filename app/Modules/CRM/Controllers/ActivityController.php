<?php

namespace Modules\CRM\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CRM\Models\Activity;

class ActivityController extends Controller
{

    public function index(Request $request)
    {
        $query = Activity::query();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('record_id')) {
            $query->where('record_id', $request->record_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function($q2) use ($q) {
                $q2->where('action', 'like', "%$q%")
                   ->orWhere('module', 'like', "%$q%")
                   ->orWhere('details', 'like', "%$q%")
                   ->orWhere('meta', 'like', "%$q%");
            });
        }

        $perPage = $request->get('per_page', 20);

        $activities = $query->with('user')->latest()->paginate($perPage);

        return response()->json($activities);
    }

    // optional: show single activity
    public function show($id)
    {
        $activity = Activity::with('user')->findOrFail($id);
        return response()->json($activity);
    }
}
