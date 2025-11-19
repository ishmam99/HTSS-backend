<?php

namespace App\Models;

use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnsiteSupportTicket extends Model
{
    use HasFactory,HasAdvancedQuery;

    protected $guarded = ['id'];
}
