<?php

namespace Modules\CRM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class RecordUserAssignment extends Model
{
    protected $guarded = ['id'];


    public function record()
    {
        return $this->belongsTo(Record::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
