<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Competency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * The parent General Skill.
     */
    public function generalSkills(): BelongsToMany
    {
        return $this->belongsToMany(
            GeneralSkill::class,
            'competency_general_skill',   // pivot table
            'competency_id',              // FK of this model
            'general_skill_id'            // FK of related model
        )->withTimestamps();
    }
}
