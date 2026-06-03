<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrainingScheduleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'training_course_id' => $this->training_course_id,
            'date' => $this->date,
            'training_course' => $this->whenLoaded('trainingCourse'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
