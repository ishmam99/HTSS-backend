<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $guarded = [];
     public function values()
    {
        return $this->hasMany(RecordValue::class,'record_id')
         ->with('field')
        ->join('module_fields', 'record_values.field_id', '=', 'module_fields.id')
        ->orderBy('module_fields.order', 'asc')
        ->select('record_values.*');
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
