<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrainingEventResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'training_type' => $this->training_type,
            'platform' => $this->platform,
            'trainer_id' => $this->trainer_id,
            'status' => $this->status,
            'trainingCourse' => $this->whenLoaded('trainingCourse'),
            'trainer' => $this->whenLoaded('trainer.user'),
        ];
    }
}
