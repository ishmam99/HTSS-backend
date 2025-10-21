<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\EnumHelper;
use App\Enums\RoleEnum;
use Illuminate\Http\JsonResponse;

class EnumController extends Controller
{
    public function roles(): JsonResponse
    {
        return response()->json([
            'roles' => EnumHelper::toArray(RoleEnum::class)
        ]);
    }
}
