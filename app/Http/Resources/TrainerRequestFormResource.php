<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrainerRequestFormResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            // Add other fields you want to return
        ];
    }
}
