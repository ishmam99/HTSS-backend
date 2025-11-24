<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'user_id', 'action', 'module', 'record_id', 'details', 'ip', 'user_agent', 'meta'
    ];

    protected $casts = [
        'details' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
