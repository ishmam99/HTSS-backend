<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftwareRequest extends Model
{
    //
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    public function software()
    {
        return $this->belongsTo(Software::class, 'software_id');
    }
    public function solution()
    {
        return $this->belongsTo(Solution::class, 'solution_id');
    }
}
