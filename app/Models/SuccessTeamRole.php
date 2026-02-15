<?php

namespace App\Models;

use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Model;

class SuccessTeamRole extends Model
{
       use HasAdvancedQuery;
    protected $guarded = ['id'];
    /**
     * Get the user that owns the SuccessTeamRole
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
