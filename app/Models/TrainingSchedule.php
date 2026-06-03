<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingSchedule extends Model
{
    protected $guarded = ['id'];

    public function trainingCourse()
    {
        return $this->belongsTo(TrainingCourse::class);
    }
}
