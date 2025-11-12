<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\CRM\Models\Record;

class Proposal extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function account()
    {
        return $this->belongsTo(Record::class, 'account_id');
    }

    public function deal()
    {
        return $this->belongsTo(Record::class, 'deal_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}