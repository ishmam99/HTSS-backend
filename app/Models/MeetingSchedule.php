<?php
namespace App\Models;

use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Model;

class MeetingSchedule extends Model
{
    use HasAdvancedQuery;
    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class, 'meeting_schedule_user');
    }

    public function successTeam()
    {
        return $this->belongsTo(SuccessTeam::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
