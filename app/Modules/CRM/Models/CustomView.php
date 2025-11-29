<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;

class CustomView extends Model
{
    protected $guarded = [];
    

    public function groups()
    {
        return $this->hasMany(CustomViewGroup::class);
    }

    public function rootGroup()
    {
        return $this->hasOne(CustomViewGroup::class)->whereNull('parent_id');
    }
}
