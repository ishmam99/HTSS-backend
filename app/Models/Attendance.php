<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $guarded = ['id'];

     public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function times()
    {
        return $this->hasMany(AttendanceTime::class);
    }

    public function attendanceInfo()
    {
        return $this->hasMany(AttendanceInfo::class);
    }
}
