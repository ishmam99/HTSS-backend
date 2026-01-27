<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\EnumHelper;
use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\JsonResponse;

class EnumController extends Controller
{
    public function roles(): JsonResponse
    {
        return response()->json([
            'roles' => EnumHelper::toArray(RoleEnum::class)
        ]);
    }

    public function roleWiseCount()
    {
        $data = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function roleWiseList(Request $request)
    {
        if ($request->filled('role')) {
            $users = User::where('role', $request->role)->get();

            return response()->json([
                'status' => true,
                'role' => $request->role,
                'data' => $users,
            ]);
        }
        $data = User::all()->groupBy('role');

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }
}
