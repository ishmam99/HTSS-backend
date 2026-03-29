<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppliedJob extends Model
{
    protected $guarded = ['id'];
    public function job()
    {
        return $this->belongsTo(JobOffer::class,'job_id');
    }

    public function software()
    {
        return $this->belongsTo(Software::class,'software_id');
    }

    public function industry()
    {
        return $this->belongsTo(Industry::class,'industry_id');
    }
}
