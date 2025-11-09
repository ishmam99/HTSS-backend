<?php

namespace App\Models;

use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Industry extends Model
{
    use HasFactory,HasAdvancedQuery;
    protected $guarded = ['id'];
    
        protected array $searchable = ['software.name','solution.name', 'name','status'];
      public function solutions()
    {
        return $this->belongsToMany(Solution::class, 'industry_solutions');
    }
    public function softwares()
    {
        return $this->belongsToMany(Software::class, 'industry_software');
    }
    /**
     * Get all of the customers for the Industry
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
      public function trainings(): HasMany
    {
        return $this->hasMany(TrainingCourse::class);
    }
}
