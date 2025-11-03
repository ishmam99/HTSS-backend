<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $guarded = ['id'];

    public function sessions()
    {
        return $this->hasMany(TrainingSession::class);
    }

    public function enrollments()
    {
        return $this->hasMany(TrainingEnrollment::class);
    }
}
