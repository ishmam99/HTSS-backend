<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EndUserSolution extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
      public function solution(): BelongsTo
    {
        return $this->belongsTo(Solution::class);
    }
    public function endUser(): BelongsTo
    {
        return $this->belongsTo(EndUser::class);
    }
}
