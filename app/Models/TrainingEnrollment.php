<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingEnrollment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function endUser()
    {
        return $this->belongsTo(EndUser::class);
    }

    public function trainingCourseSchedule()
    {
        return $this->belongsTo(TrainingCourseSchedule::class);
    }
    public function trainingRequest()
    {
        return $this->belongsTo(TrainingRequest::class);
    }
}