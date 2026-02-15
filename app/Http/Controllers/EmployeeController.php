<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        // Get all software with related skill
        $query = Employee::advancedQuery($request);
        $lists = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();
        return response()->json([
            'success' => true,
            'data' => $lists,
             'total' => Employee::count()
        ]);
    }

    /**
     * Store new employee
     */
    public function store(Request $request)
    {
         $request['joined_at'] = Carbon::parse($request->joined_at);
        $validated = $request->validate([
            'email' => 'required|email|unique:employees,email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'employee_uid' => 'required|string|unique:employees,employee_uid',
            'department_id' => 'required|exists:departments,id',
            // 'position_id' => 'required|exists:positions,id',
            'joined_at' => 'required|date',
        ]);

        DB::beginTransaction();

        try {

            // Check if user exists
            $user = User::where('email', $validated['email'])->first();

            // If not exists, create user
            if (!$user) {
                $user = User::create([
                    'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'email' => $validated['email'],
                    'password' => Hash::make('12345678'), // random password
                ]);
            }
            $empCheck = Employee::where('email',$request->email)->first();

            if($empCheck)
                {
                    return response()->json(['Employee Already exists']);
                }
            // Create employee

            $employee = Employee::create([
                ...$request->all(),
                'user_id' => $user->id,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Employee created successfully',
                'data' => $employee->load(['user','department','position'])
            ], 201);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show single employee
     */
    public function show(Employee $employee)
    {
        return response()->json(
            $employee->load([
                'user',
                'department',
                'position',
                'system'
            ])
        );
    }

    /**
     * Update employee
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'department_id' => 'sometimes|exists:departments,id',
            'position_id' => 'sometimes|exists:positions,id',
        ]);

        DB::beginTransaction();

        try {

            // Check or create user
            $user = User::where('email', $validated['email'])->first();

            if (!$user) {
                $user = User::create([
                    'name' => $employee->first_name . ' ' . $employee->last_name,
                    'email' => $validated['email'],
                   'password' => Hash::make('12345678'), // random password
                ]);
            }

            // Update employee
            $employee->update([
                ...$request->all(),
                'user_id' => $user->id,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Employee updated successfully',
                'data' => $employee->load(['user','department','position'])
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to update employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete employee
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return response()->json([
            'message' => 'Employee deleted successfully'
        ]);
    }

}
