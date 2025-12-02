<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrainingEnrollmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'endUser' => $this->whenLoaded('endUser'),
            'trainingOffer' => $this->whenLoaded('trainingOffer'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
