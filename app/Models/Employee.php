<?php

namespace App\Models;

use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{

    use HasAdvancedQuery;
    protected $guarded = ['id'];
    public function user()
{
    return $this->belongsTo(User::class);
}

public function department()
{
    return $this->belongsTo(Department::class);
}

public function position()
{
    return $this->belongsTo(EmployeePosition::class, 'position_id', 'id');
}
}
