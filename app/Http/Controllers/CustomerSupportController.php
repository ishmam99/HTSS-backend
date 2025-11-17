<?php

namespace App\Http\Controllers;

use App\Models\CustomerSupport;
use App\Http\Requests\CustomerSupportRequest;
use App\Http\Resources\CustomerSupportResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class CustomerSupportController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomerSupport::with(['solution', 'software','endUser','customer'])
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->customer_id, function ($query, $customer_id) {
                $query->where('customer_id', $customer_id);
            }
            )->when($request->end_user_id, function ($query, $end_user_id) {
                $query->where('end_user_id', $end_user_id);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('id', 'desc');

        $lists = $request->per_page ? $query->paginate($request->per_page) : $query->get();

        return CustomerSupportResource::collection($lists);
    }


    public function store(CustomerSupportRequest $request)
    {
        $data = $request->validated();

        $customerSupport = CustomerSupport::create($data);

        
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('uploads/customerSupport', 'public');
            $customerSupport->update(['attachment' => $path]);
        }
       

        return response()->json([
            'status' => true,
            'message' => 'CustomerSupport created successfully',
        ], 201);
    }

    public function show(CustomerSupport $customerSupport)
    {
        return new CustomerSupportResource($customerSupport);
    }

    public function update(CustomerSupportRequest $request, CustomerSupport $customerSupport)
    {
        $data = $request->validated();

        
        if ($request->hasFile('attachment')) {
           
            if ($customerSupport->attachment && Storage::disk('public')->exists($customerSupport->attachment)) {
                Storage::disk('public')->delete($customerSupport->attachment);
            }

            
            $path = $request->file('attachment')->store('uploads/customerSupport', 'public');
            $data['attachment'] = $path;
        }
        $customerSupport->update($data);
        return response()->json([
            'status' => true,
            'message' => 'CustomerSupport updated successfully',
        ], 200);
    }

    public function destroy(CustomerSupport $customerSupport)
    {
        $customerSupport->delete();
        return response()->json(['status' => true,'message' => 'CustomerSupport deleted successfully'],200);
    }

    public function statusUpdate(Request $request, CustomerSupport $customerSupport)
    {
        $request->validate([
            'status' => 'required',
        ]);

        $customerSupport->status = $request->status;
        $customerSupport->save();

        return response()->json([
            'status' => true,
            'message' => 'CustomerSupport status updated successfully',
        ], 200);
    }
}
