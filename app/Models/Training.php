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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function software()
    {
        return $this->belongsTo(Software::class);
    }

    public function solution()
    {
        return $this->belongsTo(Solution::class);
    }

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }
}
