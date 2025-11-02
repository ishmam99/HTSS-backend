<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftwareSkill extends Model
{

    protected $fillable = ['name', 'category', 'description', 'skill' , 'status'];
    // public function users()
    // {
    //     return $this->belongsToMany(User::class, 'user_software_skills');
    // }

    public function software()
    {
        return $this->hasMany(Software::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_software_skills')
                    ->withPivot('proficiency_level', 'experience_years')
                    ->withTimestamps();
    }
}
