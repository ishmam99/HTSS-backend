<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\PartnerResource;
use Illuminate\Validation\Rule;
use App\Models\Partner;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $partners = Partner::with('user')->paginate(10);
        return PartnerResource::collection($partners);
    }
    public function show($id)
    {
        $partner = Partner::with('user')->findOrFail($id);
        return new PartnerResource($partner);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'phone' => 'nullable|string',
            'company_name' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'website' => 'nullable|url',
            'partner_type' => 'nullable|string',
            'gender' => 'nullable|string',
        ]);

        try {
            $partner = Partner::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Partner created successfully',
                'id' => $partner->id
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Partner creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        $partner = Partner::findOrFail($id);

        $validated = $request->validate([
            'phone' => 'nullable|string',
            'company_name' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'website' => 'nullable|url',
            'partner_type' => 'nullable|string',
            'gender' => 'nullable|string',
            'status' => 'nullable|integer',
        ]);

        $partner->update($validated);
        $partner->load('user');
        return new PartnerResource($partner);
    }
    public function destroy($id)
    {
        $partner = Partner::findOrFail($id);
        $partner->delete();
        return response()->json(['message' => 'Partner deleted successfully']);
    }
}
