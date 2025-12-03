<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainerCourse extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function trainingCourse()
    {
        return $this->belongsTo(TrainingCourse::class, 'training_course_id');
    }
}
