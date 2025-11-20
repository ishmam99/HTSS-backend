<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerSupport extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function solution()
    {
        return $this->belongsTo(Solution::class);
    }

    public function software()
    {
        return $this->belongsTo(Software::class);
    }

    public function endUser()
    {
        return $this->belongsTo(EndUser::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

}
