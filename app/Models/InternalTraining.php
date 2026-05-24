<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternalTraining extends Model
{
    protected $guarded = ['id'];

    public function software()
    {
        return $this->belongsTo(Software::class);
    }

    public function solution()
    {
        return $this->belongsTo(Solution::class);
    }
}
