<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::with('user');
        if ($request->has('per_page')) {
            $lists = $customers->paginate($request->per_page);
        } else {
            $lists = $customers->get();
        }
        return CustomerResource::collection($lists);
    }

    public function show($id)
    {
        $customer = Customer::with('user')->findOrFail($id);
        return new CustomerResource($customer);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|min:6',
            'role' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string',
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make('12345678'),
                'role' => 'customer'
            ]);

            if (!empty($validated['role'])) {
                $user->role = $validated['role'];
                $user->save();
            }

            $customer = Customer::create([
                'user_id' => $user->id,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'country' => $validated['country'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
            ]);

            $customer->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully',
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'data' => new CustomerResource($customer),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Customer creation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::with('user')->findOrFail($id);
        $user = $customer->user;

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string',
            'status' => 'nullable|integer',
        ]);

        try {
            if (isset($validated['name'])) $user->name = $validated['name'];
            if (isset($validated['email'])) $user->email = $validated['email'];
            if (!empty($validated['role'])) $user->role = $validated['role'];
            $user->save();

            $customer->update([
                'phone' => $validated['phone'] ?? $customer->phone,
                'address' => $validated['address'] ?? $customer->address,
                'city' => $validated['city'] ?? $customer->city,
                'country' => $validated['country'] ?? $customer->country,
                'postal_code' => $validated['postal_code'] ?? $customer->postal_code,
                'date_of_birth' => $validated['date_of_birth'] ?? $customer->date_of_birth,
                'gender' => $validated['gender'] ?? $customer->gender,
                'status' => $validated['status'] ?? $customer->status,
            ]);

            $customer->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully',
                'data' => new CustomerResource($customer),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Customer update failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $customer = Customer::with('user')->findOrFail($id);
            $user = $customer->user;

            $customer->delete();
            if ($user) $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Customer and associated user deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete customer',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function stats()
    {
        $customers = Customer::all();
        $pending_customer = $customers->where('status',0)->count();
        $pending_customer = $customers->where('status',0)->count();
    }
}
