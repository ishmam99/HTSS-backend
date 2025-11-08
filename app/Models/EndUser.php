<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EndUser extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }

    public function softwares()
    {
        return $this->belongsToMany(Software::class, 'end_user_software');
    }
    public function softwareLevels()
    {
        return $this->hasMany(EndUserSoftware::class);
    }
}
