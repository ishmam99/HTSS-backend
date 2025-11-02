<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SolutionTraining extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function trainingSchedule()
    {
        return $this->belongsTo(TrainingSchedule::class);
    }
}
