<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecordRelation extends Model
{
    protected $guarded = [];
    /**
     * Get the parent that owns the RecordRelation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Record::class, 'parent_record_id');
    }
    public function child(): BelongsTo
    {
        return $this->belongsTo(Record::class, 'child_record_id');
    }
}
