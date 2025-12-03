<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrainerScheduleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,

            'trainer_id' => $this->trainer,
            'days' => $this->days ? json_decode($this->days, true) : null,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'training_courses' => $this->trainingCourse,
        ];
    }
}
