<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrainerCourseResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'training_course' => $this->trainingCourse->with('software,solution,industry'),
            'trainer_id' => $this->trainer,
            'status' => $this->status,
        ];
    }
}
