<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrainingOfferResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'location' => $this->location,
            'price' => $this->price,
            'available_seats' => $this->available_seats,
            'status' => $this->status,
            'trainingEvent' => $this->whenLoaded('event'),
        ];
    }
}
