<?php

namespace App\Models;

use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    use HasFactory, HasAdvancedQuery;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trainingEvents()
    {
        return $this->hasMany(TrainingEvent::class);
    }
    public function skills()
    {
        return $this->hasMany(TrainerSkill::class, 'user_id','trainer_id');
    }
    public function courses()
    {
        return $this->hasMany(TrainerCourse::class, 'user_id','trainer_id');
    }
    public function schedules()
    {
        return $this->hasMany(TrainerSchedule::class, 'user_id','trainer_id');
    }
    public function preferedSchedules()
    {
        return $this->hasMany(TrainerPreferdSchedule::class, 'user_id','trainer_id');
    }
}
