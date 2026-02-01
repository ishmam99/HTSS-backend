<?php

namespace App\Models;

use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuccessTeamTaskOutput extends Model
{
    //
    use HasAdvancedQuery;
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    /**
     * Get the successTeamTask that owns the SuccessTeamTaskOutput
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function successTeamTask(): BelongsTo
    {
        return $this->belongsTo(SuccessTeamTask::class);
    }
}
