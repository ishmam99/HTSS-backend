<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledMessage extends Model
{
    protected $fillable = [
        'applied_job_id',
        'scheduled_date',
        'scheduled_time',
        'description',
    ];
    protected $casts = [
        'scheduled_date' => 'date',
    ];

    public function appliedJob()
    {
        return $this->belongsTo(AppliedJob::class, 'applied_job_id');
    }
}
