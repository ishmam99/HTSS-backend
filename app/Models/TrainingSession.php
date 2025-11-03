<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrainingSession extends Model
{
    use HasFactory;

     protected $guarded = ['id'];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}
