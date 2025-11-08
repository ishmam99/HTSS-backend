<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    /**
     * Get all of the customers for the Industry
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
