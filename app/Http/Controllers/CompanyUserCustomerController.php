<?php

namespace App\Http\Controllers;

use App\Models\CompanyUserCustomer;
use Illuminate\Http\Request;

class CompanyUserCustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = CompanyUserCustomer::with(['company', 'user', 'customer', 'assignedBy']);

        if ($request->has('company_id')) {
            $query->where('company_id', $request->input('company_id'));
        }
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        $customers = $query->paginate(15);
        return response()->json($customers, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'user_id' => 'required|exists:users,id',
            'customer_id' => 'required|exists:customers,id',
            'assigned_by' => 'required|exists:users,id',
        ]);

        $record = CompanyUserCustomer::create($validated);
        return response()->json($record, 201);
    }

    public function show($id)
    {
        $record = CompanyUserCustomer::with(['company', 'user', 'customer', 'assignedBy'])->findOrFail($id);
        return response()->json($record, 200);
    }

    public function update(Request $request, $id)
    {
        $record = CompanyUserCustomer::findOrFail($id);
        $validated = $request->validate([
            'company_id' => 'exists:companies,id',
            'user_id' => 'exists:users,id',
            'customer_id' => 'exists:customers,id',
            'assigned_by' => 'exists:users,id',
        ]);

        $record->update($validated);
        return response()->json($record, 200);
    }

    public function destroy($id)
    {
        CompanyUserCustomer::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

}
