<?php

namespace App\Models;

use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Partner extends Model
{
    use HasFactory,HasAdvancedQuery;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
