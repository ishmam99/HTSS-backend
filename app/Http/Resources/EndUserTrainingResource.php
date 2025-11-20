<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EndUserTrainingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=> $this->id,
            'end_user' => new EndUserResource($this->whenLoaded('user')),
            'training_offer' => new TrainingOfferResource($this->whenLoaded('offer'))
        ];
    }
}
