<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $fillable = ['title', 'description', 'start_date', 'end_date' , 'status'];

    public function sessions()
    {
        return $this->hasMany(TrainingSession::class);
    }

    public function enrollments()
    {
        return $this->hasMany(TrainingEnrollment::class);
    }
}
