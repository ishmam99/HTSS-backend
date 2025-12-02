<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SoftwareLevelResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'industry' => $this->industry,
            'solution' => $this->solution,
            'software' => $this->software,
            'trainer' => $this->trainer,
            'levels' => $this->levels,
            'status' => $this->status,
        ];
    }
}
