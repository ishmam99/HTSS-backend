<?php

namespace App\Models;

use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EndUser extends Model
{
    use HasFactory,HasAdvancedQuery;

    protected $guarded = ['id'];
    protected array $searchable = ['user.name','user.email','email', 'first_name','last_name' ,'industry_id','industry.name','status'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }

    public function softwares()
    {
        return $this->belongsToMany(Software::class, 'end_user_software')->withPivot('level') // include pivot column
        ->withTimestamps();
    }
    public function solutions()
    {
        return $this->belongsToMany(Solution::class, 'end_user_solutions');
    }
    public function softwareLevels()
    {
        return $this->hasMany(EndUserSoftware::class);
    }
    /**
     * Get all of the trainingEnrollment for the EndUser
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function trainingEnrollment(): HasMany
    {
        return $this->hasMany(EndUserTraining::class , 'end_user_id');
    }
}
