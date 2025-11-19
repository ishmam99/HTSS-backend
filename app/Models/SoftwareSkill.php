<?php

namespace App\Models;

use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Model;

class SoftwareSkill extends Model
{
     use HasAdvancedQuery;
    protected $guarded = ['id'];
       protected array $searchable = ['software.name','name','status'];

    public function software()
    {
        return $this->belongsTo(Software::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_software_skills')
                    ->withPivot('proficiency_level', 'experience_years')
                    ->withTimestamps();
    }
}
