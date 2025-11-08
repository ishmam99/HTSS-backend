<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solution extends Model
{
  protected $guarded = ['id'];
     public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
     public function softwares()
    {
        return $this->belongsToMany(Software::class, 'industry_software');
    }
    public function industries()
    {
        return $this->belongsToMany(Industry::class, 'industry_solutions');
    }
}
