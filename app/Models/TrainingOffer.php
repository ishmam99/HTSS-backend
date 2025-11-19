<?php

namespace App\Models;

use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingOffer extends Model
{
    use HasFactory,HasAdvancedQuery;

    protected $guarded = ['id'];

    public function event()
    {
        return $this->belongsTo(TrainingEvent::class,'training_event_id');
    }
    /**
     * Get all of the enrolledUser for the TrainingOffer
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enrolledUser(): HasMany
    {
        return $this->hasMany(EndUserTraining::class, 'training_offer_id', 'id');
    }



}
