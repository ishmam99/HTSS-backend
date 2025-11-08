<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Industry extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
      public function solutions()
    {
        return $this->belongsToMany(Solution::class, 'industry_solutions');
    }
    public function softwares()
    {
        return $this->belongsToMany(Software::class, 'industry_software');
    }
}
