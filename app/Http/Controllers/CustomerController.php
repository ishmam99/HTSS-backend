<?php
namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\CustomerUserAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query     = Customer::advancedQuery($request);
        $customers = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();
        return response()->json([
            'success' => true,
            'data'    => $customers,
            'total'   => Customer::count(),
        ]);
    }

    public function show($id)
    {
        $customer = Customer::with('user','endUsers', 'softwares', 'solutions', 'tickets')->findOrFail($id);

        return new CustomerResource($customer);
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
             'record_id'     => 'nullable',
             'company_id'   => 'nullable|exists:companies,id',
        ]);

        try {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make('12345678'),
                'role'     => 'customer',
            ]);

            if (! empty($validated['role'])) {
                $user->role = $validated['role'];
                $user->save();
            }

            $customer = Customer::create([
                'user_id'       => $user->id,
                'record_id'    => $validated['record_id'] ?? null,
                'phone'         => $validated['phone'] ?? null,
                'address'       => $validated['address'] ?? null,
                'city'          => $validated['city'] ?? null,
                'country'       => $validated['country'] ?? null,
                'postal_code'   => $validated['postal_code'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender'        => $validated['gender'] ?? null,
                'company_id'    => $validated['company_id'] ?? null,
            ]);

            $customer->load('user');

            return response()->json([
                'success'     => true,
                'message'     => 'Customer created successfully',
                'user_id'     => $user->id,
                'customer_id' => $customer->id,
                'data'        => new CustomerResource($customer),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Customer creation failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::with('user')->findOrFail($id);
        $user     = $customer->user;

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
            if (isset($validated['name'])) {
                $user->name = $validated['name'];
            }

            if (isset($validated['email'])) {
                $user->email = $validated['email'];
            }

            if (! empty($validated['role'])) {
                $user->role = $validated['role'];
            }

            $user->save();

            $customer->update([
                'phone'         => $validated['phone'] ?? $customer->phone,
                'address'       => $validated['address'] ?? $customer->address,
                'city'          => $validated['city'] ?? $customer->city,
                'country'       => $validated['country'] ?? $customer->country,
                'postal_code'   => $validated['postal_code'] ?? $customer->postal_code,
                'date_of_birth' => $validated['date_of_birth'] ?? $customer->date_of_birth,
                'gender'        => $validated['gender'] ?? $customer->gender,
                'status'        => $validated['status'] ?? $customer->status,
                
            ]);

            $customer->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully',
                'data'    => new CustomerResource($customer),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Customer update failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $customer = Customer::with('user')->findOrFail($id);
            $user     = $customer->user;

            $customer->delete();
            if ($user) {
                $user->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Customer and associated user deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete customer',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function stats()
    {
        $customers        = Customer::all();
        $pending_customer = $customers->where('status', 0)->count();
        $pending_customer = $customers->where('status', 0)->count();
    }

    public function assignCustomer(Customer $customer, Request $request)
    {
        //BULK ASSIGNMENT
        if ($request->has('assignments')) {

            $request->validate([
                'assignments'                    => 'required|array|min:1',
                'assignments.*.user_id'          => 'required|exists:users,id',
                'assignments.*.role'             => 'required|string|max:50',
                'assignments.*.permission_level' => 'nullable|string|max:50',
            ]);

            $results = [];

            foreach ($request->assignments as $assign) {

                $existingRole = CustomerUserAssignment::where('customer_id', $customer->id)
                    ->where('role', $assign['role'])
                    ->first();

                if ($existingRole) {
                    // Replace user for same role
                    $existingRole->update([
                        'user_id'          => $assign['user_id'],
                        'permission_level' => $assign['permission_level'] ?? null,
                        'assigned_by'      => Auth::id(),
                        'assigned_at'      => now(),
                    ]);

                    $results[] = [
                        'action' => 'updated',
                        'role'   => $assign['role'],
                        'data'   => $existingRole,
                    ];
                    continue;
                }

                // Create new assignment
                $newAssignment = CustomerUserAssignment::create([
                    'customer_id'      => $customer->id,
                    'user_id'          => $assign['user_id'],
                    'role'             => $assign['role'],
                    'permission_level' => $assign['permission_level'] ?? null,
                    'assigned_by'      => Auth::id(),
                    'assigned_at'      => now(),
                ]);

                $results[] = [
                    'action' => 'created',
                    'role'   => $assign['role'],
                    'data'   => $newAssignment,
                ];
            }

            return response()->json([
                'message' => 'Bulk customer assignment processed successfully.',
                'results' => $results,
            ]);
        }
        // SINGLE ASSIGNMENT
        $request->validate([
            'user_id'          => 'required|exists:users,id',
            'role'             => 'required|string|max:50',
            'permission_level' => 'nullable|string|max:50',
        ]);

        $existingRole = CustomerUserAssignment::where('customer_id', $customer->id)
            ->where('role', $request->role)
            ->first();

        if ($existingRole) {
            $existingRole->update([
                'user_id'          => $request->user_id,
                'permission_level' => $request->permission_level,
                'assigned_by'      => Auth::id(),
                'assigned_at'      => now(),
            ]);

            return response()->json([
                'message' => 'Customer role reassigned successfully.',
                'data'    => $existingRole,
            ]);
        }

        $assignment = CustomerUserAssignment::create([
            'customer_id'      => $customer->id,
            'user_id'          => $request->user_id,
            'role'             => $request->role,
            'permission_level' => $request->permission_level,
            'assigned_by'      => Auth::id(),
            'assigned_at'      => now(),
        ]);

        return response()->json([
            'message' => 'Customer assigned successfully.',
            'data'    => $assignment,
        ]);
    }
    public function getByUser($userId)
{
    $customers = Customer::whereHas('assignments', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
        ->with(['user:id,name']) // eager load user name
        ->select('id', 'user_id') // customers table থেকে id এবং user_id
        ->get()
        ->map(function ($customer) {
            return [
                'id'   => $customer->id,
                'name' => $customer->user->name ?? null,
            ];
        });

    return response()->json([
        'success' => true,
        'data'    => $customers,
    ]);
}


}
