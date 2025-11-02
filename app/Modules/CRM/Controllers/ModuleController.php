<?php

namespace Modules\CRM\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CRM\Models\Module;

class ModuleController extends Controller
{
   public function index()
   {
     $modules = Module::all();
     return response()->json($modules);
   }
}
