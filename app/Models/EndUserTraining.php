<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EndUserTraining extends Model
{
    //
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
  /**
   * Get the user that owns the EndUserTraining
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function user(): BelongsTo
  {
      return $this->belongsTo(EndUser::class,'end_user_id');
  }
  public function offer(): BelongsTo
  {
      return $this->belongsTo(TrainingOffer::class,'training_offer_id');
  }
}
