<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceTimeResource extends JsonResource
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
            'record_id' => $this->record_id,
            'type_of_work' => $this->type_of_work,
            'notes' => $this->notes,
            'total_minute' => $this->total_minute,
            'status' => $this->status,
            'attachment' => $this->attachment 
                ? asset('storage/' . $this->attachment) 
                : null,
                
        ];
    }
}
