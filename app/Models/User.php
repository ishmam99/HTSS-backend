<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\SuccessTeam;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasAdvancedQuery;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function softwareSkills()
    {
        return $this->belongsToMany(SoftwareSkill::class, 'user_software_skills')
            ->withPivot('proficiency_level', 'experience_years')
            ->withTimestamps();
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }
    public function endUser()
    {
        return $this->hasOne(EndUser::class);
    }
    public function successTeams()
    {
        return $this->belongsToMany(
            SuccessTeam::class,
            'success_team_users',
            'user_id',
            'success_team_id'
        )->withPivot('role')->withTimestamps();
    }
    public function professionalReferences()
    {
        return $this->hasMany(ProfessionalReference::class);
    }
}
