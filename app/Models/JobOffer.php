<?php

namespace App\Models;

use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Model;

class JobOffer extends Model
{
     use HasAdvancedQuery;
    protected $guarded = ['id'];

    protected $casts = [
        'requirements' => 'array',
        'key_responsibilities' => 'array',
        'required_qualifications' => 'array',
        'key_skills' => 'array',
        'primary_software' => 'array',
        'deadline' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
