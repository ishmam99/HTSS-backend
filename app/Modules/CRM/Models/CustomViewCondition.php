<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;

class CustomViewCondition extends Model
{
    protected $guarded = ['id'];
        protected $casts = [
        'value' => 'json',
    ];
}
