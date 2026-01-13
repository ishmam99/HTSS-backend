<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerSuccessManager extends Model
{
    use HasFactory, HasAdvancedQuery;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customers()
    {
        return $this->belongsToMany(
            Customer::class,
            'customer_user_assignments',
            'user_id',
            'customer_id'
        )->select('id', 'name');
    }
}
