<?php

namespace App\Models;

use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Model;

class SuccessTeamRole extends Model
{
       use HasAdvancedQuery;
    protected $guarded = ['id'];
}
