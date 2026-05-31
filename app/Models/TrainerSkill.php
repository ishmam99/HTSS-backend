<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainerSkill extends Model
{
    //
     protected $guarded = ['id'];
        public function trainer()
    {
        return $this->belongsTo(Trainer::class, 'trainer_id');
    }
        public function trainerRequestForm()
    {
        return $this->belongsTo(TrainerRequestForm::class, 'trainer_request_form_id');
    }
        public function software()
    {
        return $this->belongsTo(Software::class, 'software_id');
    }
        public function solution()
    {
        return $this->belongsTo(Solution::class, 'solution_id');
    }
}
