<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $guarded = ['id'];
     public function fields()
    {
        return $this->hasMany(ModuleField::class);
    }

    public function records()
    {
        return $this->hasMany(Record::class);
    }
}
