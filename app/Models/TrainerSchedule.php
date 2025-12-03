<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainerSchedule extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

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
