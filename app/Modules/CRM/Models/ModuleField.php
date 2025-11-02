<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleField extends Model
{
    protected $guarded = [];

    protected $casts = [
        'options' => 'array',
    ];
    
     public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
