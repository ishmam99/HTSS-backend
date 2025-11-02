<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solution extends Model
{
    protected $fillable = ['name', 'domain', 'description' ,'user_id' , 'status'];
     public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
