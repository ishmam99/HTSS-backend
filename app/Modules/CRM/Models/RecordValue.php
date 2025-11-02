<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;

class RecordValue extends Model
{
    protected $guarded = [];
       public function field()
    {
        return $this->belongsTo(ModuleField::class,'field_id');
    }

    public function record()
    {
        return $this->belongsTo(Record::class);
    }
}
