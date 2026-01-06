<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;
    use HasAdvancedQuery;
    protected $guarded = ['id'];
    
    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_department_id');
    }
}
