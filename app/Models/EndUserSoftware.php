<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EndUserSoftware extends Model
{
    protected $guarded = ['id'];
    /**
     * Get the software that owns the EndUserSoftware
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function software(): BelongsTo
    {
        return $this->belongsTo(Software::class);
    }
    public function endUser(): BelongsTo
    {
        return $this->belongsTo(EndUser::class);
    }
}
