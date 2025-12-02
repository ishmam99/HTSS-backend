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

    public function solution()
    {
        return $this->belongsTo(Solution::class);
    }

    public function software()
    {
        return $this->belongsTo(Software::class);
    }
}
