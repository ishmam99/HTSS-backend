<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;

class CustomViewGroup extends Model
{
    protected $guarded = [];
      public function conditions()
    {
        return $this->hasMany(CustomViewCondition::class, 'custom_view_group_id');
    }

    public function children()
    {
        return $this->hasMany(CustomViewGroup::class, 'parent_id');
    }
    public function childrenRecursive()
{
    return $this->children()->with('childrenRecursive.conditions');
}

}
