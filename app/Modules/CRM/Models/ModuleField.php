<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleField extends Model
{
    protected $guarded = [];
     public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
