<?php
namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'date'                 => $this->date,
            'user_id'              => $this->user_id,

            'user'                 => $this->user ? [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'email' => $this->user->email,
                'role'  => $this->user->role,
            ] : null,

            'total_working_minute' => $this->total_working_minute,
            'total_working_hours'  => number_format($this->total_working_minute / 60, 2),
            'status' => $this->status,

            'attendance_logs' => $this->attendanceInfo->map(function ($log) {
                return [
                    'id' => $log->id,
                    'login_time' => Carbon::createFromTimestamp($log->login_time)->format('Y-m-d H:i:s'),
                    'logout_time' => $log->logout_time
                        ? Carbon::createFromTimestamp($log->logout_time)->format('Y-m-d H:i:s')
                        : null,
                    'worked_minutes' => $log->logout_time
                        ? Carbon::createFromTimestamp($log->login_time)
                        ->diffInMinutes(Carbon::createFromTimestamp($log->logout_time))
                        : 0,
                    'status' => $log->status,
                ];
            }),

            'times'  => AttendanceTimeResource::collection($this->times),
        ];
    }
}
