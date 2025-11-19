<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrainingCourseResource extends JsonResource
{
    public function toArray($request): array
    {
         return [
            'id' => $this->id,
            'training_type' => $this->training_type,
            'solution' => $this->whenLoaded('solution'),
            'software' => $this->whenLoaded('software'),
            'training_level' => $this->training_level,
            'title' => $this->title,
            'course_id' => $this->course_id,
            'course_code' => $this->course_code,
            'description' => $this->description,
            'duration' => $this->duration,
            'status' => $this->status,
            'industry' => $this->whenLoaded('industry'),
            'customer' => $this->whenLoaded('customer'),
        ];
    }
}
