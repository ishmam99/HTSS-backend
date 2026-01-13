<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerUserAssignment extends Model
{
    protected $table = 'customer_user_assignments';
    protected $guarded = ['id'];


    public function customer()
    {
        return $this->belongsTo(Customer::class);
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
