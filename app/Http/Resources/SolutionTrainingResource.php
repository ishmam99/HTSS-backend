<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SolutionTrainingResource extends JsonResource
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
            'training_schedule_id' => $this->training_schedule_id,
            'title' => $this->title,
            'objective' => $this->objective,
            'content' => $this->content,
            'material_link' => $this->material_link,
            'duration_minutes' => $this->duration_minutes,
            'level' => $this->level,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'training_schedule' => $this->whenLoaded('trainingSchedule'),
        ];
    }
}
