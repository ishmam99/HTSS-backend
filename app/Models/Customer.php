<?php

namespace App\Models;

use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Customer extends Model
{
    use HasFactory,HasAdvancedQuery;
    protected $guarded = [];
     protected array $searchable = ['user.name','user.email', 'phone', 'address','city','industry_id','industry.name','status'];
    // protected array $relations = ['user', 'industry', 'softwares', 'solutions'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }
    /**
     * The softwares that belong to the Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function softwares(): BelongsToMany
    {
        return $this->belongsToMany(Software::class, 'customer_software');
    }
    public function solutions(): BelongsToMany
    {
        return $this->belongsToMany(Solution::class, 'customer_solutions');
    }
}
