<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function index(Request $request)
    {
        $query = User::advancedQuery($request);
        $customers = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();
        return response()->json([
            'success' => true,
            'data' => $customers,
            'total' => User::count()
        ]);
    }
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role'     => ['required', new Enum(RoleEnum::class)],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'  => $request->role,
        ]);

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'user'    => UserResource::make($user),
            'token'   => $token
        ], 201);
    }

    /**
     * Login existing user
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($user && in_array($user->role,  ['sales-manager', 'sales-executive', 'crm-manager', 'crm-executive'])) {
            logActivity('login', 'auth', null, [
                'data' => 'Logged in to Sales Dashboard'
            ], 'user-login', $user);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user'    => UserResource::make($user),
            'token'   => $token
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function usersByRole(Request $request)
    {
        $request->validate([
            'role' => 'required|string',
        ]);

        $role = $request->query('role');

        $q = User::query()->where('role', $role);

        if ($request->filled('name')) {
            $q->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('email')) {
            $q->where('email', 'like', '%' . $request->email . '%');
        }

        $users = $q->latest()->paginate(
            $request->integer('per_page', 10)
        );

        if ($users->isEmpty()) {
            return response()->json([
                'status'  => false,
                'message' => 'No users found for this role',
                'data'    => [],
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'User list fetched successfully',
            'data'    => $users,
        ], 200);
    }


    public function roleWiseCount(Request $request)
    {
        $roles = User::select(
            'role',
            DB::raw('COUNT(*) as total_users')
        )
            ->whereNotNull('role')
            ->groupBy('role')
            ->orderBy('role')
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Role-wise user count fetched successfully',
            'data'    => $roles,
        ], 200);
    }
    public function setRole(Request $request)
    {
        $user = User::where('id',$request->user_id)->first();
        if($user)
            {
                $user->update([
                    'role' => $request->role
                ]);
            }
            else{
                return response()->json(['User Not found']);
            }
            return response()->json(['User role updated successfully']);
    }
}
