<?php

namespace Modules\CRM\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RecordValueController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'CRM RecordValueController works!']);
    }
}