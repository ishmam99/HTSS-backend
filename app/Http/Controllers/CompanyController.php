<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Models\Customer;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
 public function index(Request $request)
    {
        $query = Company::advancedQuery($request);
         $lists = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();

        return response()->json([
            'success' => true,
            'data' => $lists,
            'total' => Company::count(),
        ]);
    }

    public function store(StoreCompanyRequest $request)
    {
        $company = Company::create($request->validated());
        return (new CompanyResource($company))->response()->setStatusCode(201);
    }

    public function show(Company $company)
    {
        return new CompanyResource($company);
    }

    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $company->update($request->validated());
        return new CompanyResource($company);
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return response()->noContent();
    }
    public function assignCustomers(Request $request, $companyId)
{
    $request->validate([
        'customer_ids' => 'required|array',
        'customer_ids.*' => 'exists:customers,id',
    ]);

    Customer::whereIn('id', $request->customer_ids)
            ->update(['company_id' => $companyId]);

    return response()->json([
        'success' => true,
        'message' => 'Customers assigned successfully.',
    ]);
}
}
