<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainerPreferdSchedule extends Model
{
    //
     protected $guarded = ['id'];

     public function trainerRequestForm()
     {
         return $this->belongsTo(TrainerRequestForm::class, 'trainer_request_form_id');
     }
     public function trainer()
     {
         return $this->belongsTo(User::class, 'trainer_id');
     }
}
