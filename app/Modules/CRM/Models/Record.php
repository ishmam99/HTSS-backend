<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $guarded = [];
     public function values()
    {
        return $this->hasMany(RecordValue::class,'record_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class,'module_id');
    }
    public function assignments()
    {
        return $this->hasMany(RecordUserAssignment::class);
    }
}
