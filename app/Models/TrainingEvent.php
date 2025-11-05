<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingEvent extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function trainingCourse()
    {
        return $this->belongsTo(TrainingCourse::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }
}
