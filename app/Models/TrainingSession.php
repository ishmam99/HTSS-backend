<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrainingSession extends Model
{
    use HasFactory;

    protected $fillable = ['training_id', 'session_title', 'session_date', 'location' , 'status'];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}
