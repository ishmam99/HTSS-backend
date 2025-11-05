<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrainingEnrollmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'endUser_id' => $this->endUser_id,
            'offer_id' => $this->offer_id,
            'status' => $this->status,
            'endUser' => $this->whenLoaded('endUser'),
            'offer' => $this->whenLoaded('offer'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
