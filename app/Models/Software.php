<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Software extends Model
{
 protected $table = 'softwares';

     protected $fillable = ['name', 'vendor', 'version', 'release_date', 'software_skill_id' , 'user_id' , 'status'];
    public function softwareSkill()
    {
        return $this->belongsTo(SoftwareSkill::class);
    }

    public function solutions()
    {
        return $this->belongsToMany(Solution::class, 'industry_solutions');
    }
    public function industries()
    {
        return $this->belongsToMany(Industry::class, 'industry_software');
    }
    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
