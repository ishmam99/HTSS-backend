<?php

namespace App\Models;

use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{

    use HasFactory,HasAdvancedQuery;
    protected $guarded = [];
     protected array $searchable = ['assignedUsers.user.name',
    'assignedUsers.user.email',
    'assignedUsers.role',
    'assignedUsers.permission_level','user.name','user.email', 'phone', 'address','city','industry_id','industry.name','status'];
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
    /**
     * Get all of the tickets for the Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(CustomerSupport::class);
    }
    public function endUsers(): HasMany
    {
        return $this->hasMany(EndUser::class);
    }


public function assignments()
{
    return $this->hasMany(CustomerUserAssignment::class);
}

public function assignedUsers()
{
    return $this->hasMany(CustomerUserAssignment::class)
                ->with('user');
}
/**
 * Get the company that owns the Customer
 *
 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
 */
public function company(): BelongsTo
{
    return $this->belongsTo(Company::class);
}

}
