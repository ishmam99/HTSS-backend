<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\CRM\Models\Record;

class AttendanceTime extends Model
{
    protected $guarded = ['id'];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function record()
    {
        return $this->belongsTo(Record::class);
    }
}
