<?php

namespace App\Models;

use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
           use HasAdvancedQuery;
      protected $fillable = [
        'name',
        'address',
        'email',
        'phone',
        'website',
    ];
    /**
     * Get all of the user for the Company
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    /**
     * Get all of the customers for the Company
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
    public function successTeams()
{
    return $this->belongsToMany(
        SuccessTeam::class,
        'success_team_companies',
        'company_id',
        'success_team_id'
    )->withTimestamps();
}
}
