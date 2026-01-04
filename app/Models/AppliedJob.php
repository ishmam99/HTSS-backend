<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppliedJob extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'contact',
        'emergency_contact',
        'system',
        'softwares',
        'industry',
        'highest_education',
        'university',
        'pdf_resume',
        'job_id',
        'software_id',
        'industry_id',
    ];

    public function job()
    {
        return $this->belongsTo(JobOffer::class);
    }

    public function software()
    {
        return $this->belongsTo(Software::class);
    }

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }
}
