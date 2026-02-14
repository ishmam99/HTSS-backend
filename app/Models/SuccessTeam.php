<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Relations\HasMany;

    class SuccessTeam extends Model
{
     use HasAdvancedQuery;
     protected $fillable = ['name', 'user_id', 'status', 'company_id'];

    // Owner of the team
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
   public function scopeWithCustomersCount($query)
{
    return $query->withCount([
        'companies as customers_count' => function ($q) {
            $q->join('customers', 'customers.company_id', '=', 'companies.id');
        }
    ]);
}

    // Companies assigned to team
    public function companies()
    {
        return $this->belongsToMany(
            Company::class,
            'success_team_companies',
            'success_team_id',
            'company_id'
        );
    }

    // Members assigned to team
    public function members()
    {
        return $this->belongsToMany(
            User::class,
            'success_team_users',
            'success_team_id',
            'user_id'
        )->withPivot('role')->withTimestamps();
    }
    /**
     * Get all of the activities for the SuccessTeam
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activities(): HasMany
    {
        return $this->hasMany(TeamActivity::class);
    }
    /**
     * Get all of the tasks for the SuccessTeam
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(SuccessTeamTask::class);
    }
}
