<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainerRequestForm extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }

    // public function solution()
    // {
    //     return $this->belongsTo(Solution::class);
    // }

    // public function software()
    // {
    //     return $this->belongsTo(Software::class);
    // }
    
     public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
    public function schedules()
    {
        return $this->hasMany(TrainerPreferdSchedule::class);
    }
    public function skills()
    {
        return $this->hasMany(TrainerSkill::class);
    }
    public function courses()
    {
        return $this->hasMany(TrainerCourse::class);
    }
    
}
