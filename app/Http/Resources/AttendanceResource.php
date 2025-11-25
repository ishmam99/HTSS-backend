<?php

namespace App\Http\Resources;

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
            'id' => $this->id,
            'date' => $this->date,
            'user_id' => $this->user_id,
            'total_working_minute' => $this->total_working_minute,
            'status' => $this->status,
            'times' => AttendanceTimeResource::collection($this->times),
        ];
    }
}
