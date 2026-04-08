<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuccessTeamActivityReport extends Model
{
    //
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    protected $casts = [
        'summary_activities' => 'array',
        'key_outcomes'       => 'array',
    ];
    /**
     * Get the user that owns the SuccessTeamActivityReport
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function successTeam(): BelongsTo
    {
        return $this->belongsTo(SuccessTeam::class);
    }
    public function getPeriodRange()
    {

        $start = Carbon::createFromFormat('F-Y', $this->period)->startOfMonth();
        $end   = Carbon::createFromFormat('F-Y', $this->period)->endOfMonth();

        return [$start, $end];
    }

    public function taskOutputs()
    {
        [$start, $end] = $this->getPeriodRange();

        return SuccessTeamTaskOutput::whereHas('successTeamTask', function ($q) {
            $q->where('success_team_id', $this->success_team_id);
        })->with('successTeamTask.assignedPerson')
            ->whereBetween('completed_at', [$start, $end]);
    }
}
