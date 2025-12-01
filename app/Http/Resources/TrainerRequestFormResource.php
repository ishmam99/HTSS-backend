<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrainerRequestFormResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'experience_year' => $this->experience_year,
            'industry_id' => $this->industry,
            'solution_id' => $this->solution,
            'software_id' => $this->software,
        ];
    }
}
