<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduledMessageResource extends JsonResource
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
            'applied_job_id' => $this->applied_job_id,
            'date' => $this->scheduled_date?->format('Y-m-d'),
            'time' => $this->scheduled_time,
            'status' => $this->status,
            'description' => $this->description,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'applied_job' => AppliedJobResource::make($this->whenLoaded('appliedJob')),
        ];
    }
}
