<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerSoftware extends Model
{
    //
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
     public function customer(): BelongsTo
     {
         return $this->belongsTo(Customer::class);
     }
     public function software(): BelongsTo
     {
         return $this->belongsTo(Software::class);
     }
}
