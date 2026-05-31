<?php

namespace App\Models;

use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Solution extends Model
{
     use HasAdvancedQuery;
  protected $guarded = ['id'];
     public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
     public function softwares()
    {
        return $this->belongsToMany(Software::class, 'software_solutions');
    }
    public function industries()
    {
        return $this->belongsToMany(Industry::class, 'industry_solutions');
    }
      public function trainings(): HasMany
    {
        return $this->hasMany(TrainingCourse::class);
    }
      public function internalTrainings(): HasMany
    {
        return $this->hasMany(TrainingCourse::class);
    }
}
