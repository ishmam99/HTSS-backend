<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\PartnerResource;
use Illuminate\Validation\Rule;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $query = Partner::advancedQuery($request);
        $lists = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();
        return response()->json([
            'success' => true,
            'data' => $lists,
             'total' => Partner::count()
        ]);
    }
    public function show($id)
    {
        $partner = Partner::with('user')->findOrFail($id);
        return new PartnerResource($partner);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|min:6',
            'phone' => 'nullable|string',
            'company_name' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'website' => 'nullable|url',
            'partner_type' => 'nullable|string',
            'gender' => 'nullable|string',
            'role' => 'nullable|string'
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make('12345678'),
            ]);

            if (!empty($validated['role'])) {
                $user->role = $validated['role'];
                $user->save();
            }

            $partner = Partner::create([
                'user_id' => $user->id,
                'phone' => $validated['phone'] ?? null,
                'company_name' => $validated['company_name'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'country' => $validated['country'] ?? null,
                'website' => $validated['website'] ?? null,
                'partner_type' => $validated['partner_type'] ?? null,
                'gender' => $validated['gender'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Partner created successfully',
                'user_id' => $user->id,
                'partner_id' => $partner->id,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Partner creation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $partner = Partner::with('user')->findOrFail($id);
        $user = $partner->user;

        $validated = $request->validate([
            'name' => 'nullable|string',
            'email' => 'nullable|string',
            'role' => 'nullable|string',
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

        try {
            if (isset($validated['name'])) {
                $user->name = $validated['name'];
            }
            if (isset($validated['email'])) {
                $user->email = $validated['email'];
            }
            if (!empty($validated['role'])) {
                $user->role = $validated['role'];
            }
            $user->save();
            $partner->update([
                'phone' => $validated['phone'] ?? $partner->phone,
                'company_name' => $validated['company_name'] ?? $partner->company_name,
                'address' => $validated['address'] ?? $partner->address,
                'city' => $validated['city'] ?? $partner->city,
                'country' => $validated['country'] ?? $partner->country,
                'website' => $validated['website'] ?? $partner->website,
                'partner_type' => $validated['partner_type'] ?? $partner->partner_type,
                'gender' => $validated['gender'] ?? $partner->gender,
                'status' => $validated['status'] ?? $partner->status,
            ]);

            $partner->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Partner updated successfully',
                'data' => new PartnerResource($partner),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Partner update failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function destroy($id)
    {
        try {
            $partner = Partner::findOrFail($id);
            $user = $partner->user;
            $partner->delete();

            if ($user) {
                $user->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Partner and associated user deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete partner',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
