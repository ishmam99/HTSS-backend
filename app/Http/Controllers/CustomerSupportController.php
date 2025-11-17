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
        $query = CustomerSupport::when($request->has('type'), function($query, $request) {
            return $query->where('type', $request->type);
        })->
        when($request->has('status'), function($query, $request) {
            return $query->where('status', $request->status);
        })->orderBy('id', 'desc');

        if ($request->has('per_page')) {
            $lists = $query->paginate($request->per_page);
        } else {
            $lists = $query->get();
        }

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
