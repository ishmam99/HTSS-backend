<?php

namespace App\Models;

use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuccessTeamTask extends Model
{
    //
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    use HasAdvancedQuery;
    protected $guarded = ['id'];
    /**
     * Get the user that owns the SuccessTeamTask
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function successTeam(): BelongsTo
    {
        return $this->belongsTo(SuccessTeam::class);
    }
    public function assignedPerson(): BelongsTo
    {
        return $this->belongsTo(User::class,'assigned_to');
    }
    /**
     * Get all of the outputs for the SuccessTeamTask
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function outputs(): HasMany
    {
        return $this->hasMany(SuccessTeamTaskOutput::class);
    }
    /**
     * Get the solution that owns the SuccessTeamTask
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
}
