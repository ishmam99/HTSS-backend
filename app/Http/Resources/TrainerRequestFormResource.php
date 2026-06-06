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
            'status' => $this->status,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'experience_year' => $this->experience_year,
            'skills' => $this->skills,
            'schedules' => $this->schedules,
             'trainer' => $this->trainer,
             'courses' => $this->courses,
            // 'industry' => $this->industry,
            // 'solution' => $this->solution,
            // 'software' => $this->software,
        ];
        
    }
}
