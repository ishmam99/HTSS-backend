<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceInfo extends Model
{
    protected $guarded = ['id'];
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    
}
