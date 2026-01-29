<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
 public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        return CompanyResource::collection(Company::paginate($perPage));
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

}
