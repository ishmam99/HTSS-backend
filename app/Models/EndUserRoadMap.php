<?php

namespace App\Models;

use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Model;

class EndUserRoadMap extends Model
{
    use HasAdvancedQuery;
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function trainingCourse()
    {
        return $this->belongsTo(TrainingCourse::class);
    }
    public function software()
    {
        return $this->belongsTo(Software::class);
    }
    public function solution()
    {
        return $this->belongsTo(Solution::class);
    }
}
