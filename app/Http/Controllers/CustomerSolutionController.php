<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerSolutionResource;
use App\Http\Resources\CustomerSolutions;
use App\Models\Customer;
use App\Models\CustomerSolution;
use Illuminate\Http\Request;

class CustomerSolutionController extends Controller
{
    //
    public function index(Request $request)
    {
        $customerIds = [];
        if ($request->filled('company_id')) {
            $customerIds = Customer::where('company_id', $request->company_id)->pluck('id');
        }
        $query = CustomerSolution::with(['customer.user', 'solution', 'solution.softwares'])
            ->when(auth()->user()->role === 'customer', function ($q) {
                $q->where('customer_id', auth()->user()->customer->id);
            })
            ->when($request->filled('customer_id'), function ($q) use ($request) {
                $q->where('customer_id', $request->customer_id);
            })
            ->when($request->filled('customer_ids'), function ($q) use ($request) {
                $ids = explode(',', $request->customer_ids);
                $q->whereIn('customer_id', $ids);
            })
            ->when($request->filled('company_id'), function ($q) use ($customerIds) {

                $q->whereIn('customer_id', $customerIds);
            })
            ->when($request->filled('solution_id'), function ($q) use ($request) {
                $q->where('solution_id', $request->solution_id);
            })
            ->when($request->filled('solution_ids'), function ($q) use ($request) {
                $ids = explode(',', $request->solution_ids);
                $q->whereIn('solution_id', $ids);
            })
            ->when($request->filled('usability'), function ($q) use ($request) {
                $q->where('usability', $request->usability);
            })
            ->when($request->filled('softwares'), function ($q) {
                $q->with('solution.softwares');
            });

        $data = $query->get();

        return response()->json(CustomerSolutionResource::collection($data));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role == 'customer') {
            $request['customer_id'] = auth()->user()->customer->id;
        }
        $request->validate([
            'solution_id' => 'required|exists:solutions,id',
            'customer_id' =>  'required|exists:customers,id',
            'usability'   => 'nullable|integer|min:0'
        ]);
        CustomerSolution::Create([
            'customer_id' => $request->customer_id,
            'solution_id' => $request->solution_id,
            'usability' => $request->usability ?? 0
        ]);
        return response()->json('Customer solution added to list');
    }

    public function update(Request $request, $id)
    {
        $data = CustomerSolution::findOrFail($id);

        if (auth()->check() && auth()->user()->role == 'customer') {
            if ($data->customer_id != auth()->user()->customer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        }

        $request->validate([
            'solution_id' => 'nullable|exists:solutions,id',
            'customer_id' => 'nullable|exists:customers,id',
            'usability'   => 'nullable|integer|min:0'
        ]);

        $updateData = [];

        if ($request->filled('customer_id')) {
            $updateData['customer_id'] = $request->customer_id;
        }

        if ($request->filled('solution_id')) {
            $updateData['solution_id'] = $request->solution_id;
        }

        if ($request->filled('usability')) {
            $updateData['usability'] = $request->usability;
        }

        $data->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Customer solution updated successfully',
            'data' => $data
        ]);
    }
}
