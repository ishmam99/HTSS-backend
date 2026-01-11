<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $guarded = [];
     public function values()
    {
        return $this->hasMany(RecordValue::class,'record_id')->with('field');
    }

    public function module()
    {
        return $this->belongsTo(Module::class,'module_id');
    }
    public function assignments()
    {
        return $this->hasMany(RecordUserAssignment::class);
    }


    public function relationsAsChild()
{
    return $this->hasMany(RecordRelation::class, 'child_record_id');
}

    public function parentRecord()
    {
        return $this->belongsTo(Record::class, 'parent_record_id');
    }

}
