<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GeneralSkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'icon',
        'proficiency_level',
    ];

    /**
     * Competencies (tags) under this skill.
     */
    public function competencies(): BelongsToMany
    {
        return $this->belongsToMany(
            Competency::class,
            'competency_general_skill',   // pivot table
            'general_skill_id',           // FK of this model
            'competency_id'               // FK of related model
        )->withTimestamps();
    }
    public function user(): HasMany
    {
        return $this->belongsTo(User::class);
    }
}
