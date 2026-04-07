<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerSoftware;
use Illuminate\Http\Request;

class CustomerSoftwareController extends Controller
{
    //
    public function index(Request $request)
    {
        $customerIds = [];
        if ($request->filled('company_id')) {
            $customerIds = Customer::where('company_id', $request->company_id)->pluck('id');
        }
        $query = CustomerSoftware::with(['customer.user', 'software'])
            ->when(auth()->check() && auth()->user()->role === 'customer', function ($q) {
                $q->where('customer_id', auth()->user()->customer->id);
            })
            ->when($request->filled('customer_id'), function ($q) use ($request) {
                $q->where('customer_id', $request->customer_id);
            })
            ->when($request->filled('company_id'), function ($q) use ($customerIds) {

                $q->whereIn('customer_id', $customerIds);
            })
            ->when($request->filled('customer_ids'), function ($q) use ($request) {
                $ids = explode(',', $request->customer_ids);
                $q->whereIn('customer_id', $ids);
            })
            ->when($request->filled('software_ids'), function ($q) use ($request) {
                $ids = explode(',', $request->software_ids);
                $q->whereIn('software_id', $ids);
            })
            ->when($request->filled('software_id'), function ($q) use ($request) {
                $q->where('software_id', $request->software_id);
            })
            ->when($request->filled('usability'), function ($q) use ($request) {
                $q->where('usability', $request->usability);
            });

        $data = $query->get();

        return response()->json($data);
    }
    public function store(Request $request)
    {
        if (auth()->user()->role == 'customer') {
            $request['customer_id'] = auth()->user()->customer->id;
        }
        $request->validate([
            'software_id' => 'required|exists:softwares,id',
            'customer_id' =>  'required|exists:customers,id',
            'usability'   => 'nullable|integer|min:0'
        ]);
        CustomerSoftware::Create([
            'customer_id' => $request->customer_id,
            'software_id' => $request->software_id,
            'usability' => $request->usability ?? 0
        ]);
        return response()->json('Customer Software added to list');
    }

    public function update(Request $request, $id)
    {
        $data = CustomerSoftware::findOrFail($id);
        if (auth()->check() && auth()->user()->role == 'customer') {
            if ($data->customer_id != auth()->user()->customer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        }
        $request->validate([
            'software_id' => 'nullable|exists:softwares,id',
            'customer_id' => 'nullable|exists:customers,id',
            'usability'   => 'nullable|integer|min:0'
        ]);
        $updateData = [];

        if ($request->filled('customer_id')) {
            $updateData['customer_id'] = $request->customer_id;
        }

        if ($request->filled('software_id')) {
            $updateData['software_id'] = $request->software_id;
        }

        if ($request->filled('usability')) {
            $updateData['usability'] = $request->usability;
        }

        // Update
        if (!empty($updateData)) {
            $data->update($updateData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Customer Software updated successfully',
            'data' => $data
        ]);
    }
}
