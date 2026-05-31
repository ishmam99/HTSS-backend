<?php

namespace App\Models;

use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Software extends Model
{
     use HasAdvancedQuery;
    protected $table = 'softwares';

     protected $fillable = ['name', 'vendor', 'version', 'release_date', 'software_skill_id' , 'user_id' , 'status'];
    public function softwareSkill()
    {
        return $this->hasMany(SoftwareSkill::class);
    }

    public function solutions()
    {
        return $this->belongsToMany(Solution::class, 'software_solutions');
    }
    public function industries()
    {
        return $this->belongsToMany(Industry::class, 'industry_software');
    }
    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    /**
     * Get all of the trainings for the Software
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function trainings(): HasMany
    {
        return $this->hasMany(TrainingCourse::class);
    }
    public function internalTrainings(): HasMany
    {
        return $this->hasMany(TrainingCourse::class);
    }
}
