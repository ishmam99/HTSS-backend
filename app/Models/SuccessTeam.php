<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class SuccessTeam extends Model
{
     protected $fillable = ['name', 'user_id', 'status', 'company_id'];

    // Owner of the team
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
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
}
