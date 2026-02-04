<?php

namespace App\Http\Controllers;

use App\Models\CustomerSoftware;
use Illuminate\Http\Request;

class CustomerSoftwareController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = CustomerSoftware::with(['customer.user', 'software'])
            ->when(auth()->check() && auth()->user()->role === 'customer', function ($q) {
                $q->where('customer_id', auth()->user()->customer->id);
            })
            ->when($request->filled('customer_id'), function ($q) use ($request) {
                $q->where('customer_id', $request->customer_id);
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
        ]);
        CustomerSoftware::firstOrcreate([
            'customer_id' => $request->customer_id,
            'software_id' => $request->software_id
        ]);
        return response()->json('Customer Software added to list');
    }
}
