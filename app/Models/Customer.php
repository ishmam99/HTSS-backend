<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Customer extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /**
     * The softwares that belong to the Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function softwares(): BelongsToMany
    {
        return $this->belongsToMany(Software::class, 'customer_softwares');
    }
    public function solutions(): BelongsToMany
    {
        return $this->belongsToMany(Solution::class, 'customer_solutions');
    }
}
