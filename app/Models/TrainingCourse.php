<?php

namespace App\Models;

use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingCourse extends Model
{
    use HasFactory,HasAdvancedQuery;

    protected $guarded = ['id'];
    /**
     * Get the solution that owns the TrainingCourse
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function solution(): BelongsTo
    {
        return $this->belongsTo(Solution::class);
    }
    public function software(): BelongsTo
    {
        return $this->belongsTo(Software::class);
    }
    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
