<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\CustomerSuccessManagerResource;
use App\Models\CustomerSuccessManager;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;

class CustomerSuccessManagerController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomerSuccessManager::with('user');

        $data = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();

        return response()->json([
            'success' => true,
            'data'    => $data,
            'total'   => CustomerSuccessManager::count(),
        ]);
    }

    public function show($id)
    {
        $manager = CustomerSuccessManager::with(['user', 'customers'])->findOrFail($id);
        return new CustomerSuccessManagerResource($manager);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'nullable|min:6',
            'role'          => 'nullable|string',
            'phone'         => 'nullable|string',
            'address'       => 'nullable|string',
            'city'          => 'nullable|string',
            'country'       => 'nullable|string',
            'postal_code'   => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender'        => 'nullable|string',
        ]);

        try {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password'] ?? '12345678'),
                'role'     => $validated['role'] ?? 'customer-success-manager',
            ]);

            $manager = CustomerSuccessManager::create([
                'user_id'       => $user->id,
                'phone'         => $validated['phone'] ?? null,
                'address'       => $validated['address'] ?? null,
                'city'          => $validated['city'] ?? null,
                'country'       => $validated['country'] ?? null,
                'postal_code'   => $validated['postal_code'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender'        => $validated['gender'] ?? null,
            ]);

            $manager->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Customer Success Manager created successfully',
                'data'    => new CustomerSuccessManagerResource($manager),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Creation failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $manager = CustomerSuccessManager::with('user')->findOrFail($id);
        $user = $manager->user;

        $validated = $request->validate([
            'name'          => 'nullable|string|max:255',
            'email'         => ['nullable', 'email', Rule::unique('users')->ignore($user->id)],
            'role'          => 'nullable|string',
            'phone'         => 'nullable|string',
            'address'       => 'nullable|string',
            'city'          => 'nullable|string',
            'country'       => 'nullable|string',
            'postal_code'   => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender'        => 'nullable|string',
            'status'        => 'nullable|integer',
        ]);

        try {
            if (isset($validated['name'])) $user->name = $validated['name'];
            if (isset($validated['email'])) $user->email = $validated['email'];
            if (!empty($validated['role'])) $user->role = $validated['role'];
            $user->save();

            $manager->update([
                'phone'         => $validated['phone'] ?? $manager->phone,
                'address'       => $validated['address'] ?? $manager->address,
                'city'          => $validated['city'] ?? $manager->city,
                'country'       => $validated['country'] ?? $manager->country,
                'postal_code'   => $validated['postal_code'] ?? $manager->postal_code,
                'date_of_birth' => $validated['date_of_birth'] ?? $manager->date_of_birth,
                'gender'        => $validated['gender'] ?? $manager->gender,
                'status'        => $validated['status'] ?? $manager->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Customer Success Manager updated successfully',
                'data'    => new CustomerSuccessManagerResource($manager),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $manager = CustomerSuccessManager::with('user')->findOrFail($id);

            if ($manager->user) {
                $manager->user->delete();
            }
            $manager->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Customer Success Manager and user deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Delete failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function getByUser($userId)
    {
        $csm = CustomerSuccessManager::where('user_id', $userId)->first();

        if (!$csm) {
            return response()->json([
                'success' => false,
                'message' => 'Customer Success Manager not found'
            ], 404);
        }

        $customers = Customer::whereHas('assignments', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->with('user:id,name')
            ->select('customers.id', 'customers.user_id')
            ->get()
            ->map(function ($customer) {
                return [
                    'id'   => $customer->id,
                    'name' => $customer->user->name ?? null
                ];
            });

        $csmData = [
            'id'          => $csm->id,
            'user_id'     => $csm->user_id,
            'user_name'   => $csm->user->name ?? null,
            'phone'       => $csm->phone,
            'address'     => $csm->address,
            'city'        => $csm->city,
            'country'     => $csm->country,
            'postal_code' => $csm->postal_code,
            'date_of_birth' => $csm->date_of_birth,
            'gender'      => $csm->gender,
            'status'      => $csm->status,
            'created_at'  => $csm->created_at,
            'updated_at'  => $csm->updated_at,
            'customers'   => $customers
        ];
        return response()->json([
            'success' => true,
            'csm'     => [$csmData]
        ]);
    }
}
