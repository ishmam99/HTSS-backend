<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerSolution extends Model
{
    //
     protected $guarded = ['id'];
     /**
      * Get the customer that owns the CustomerSolution
      *
      * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
      */
     public function customer(): BelongsTo
     {
         return $this->belongsTo(Customer::class);
     }
     public function solution(): BelongsTo
     {
         return $this->belongsTo(Solution::class);
     }
}
