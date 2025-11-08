<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrainerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'bio' => $this->bio,
            'expertise_area' => $this->expertise_area,
            'phone' => $this->phone,
            'linkedin_profile' => $this->linkedin_profile,
            'address' => $this->address,
            'image' => $this->image,
            'status' => $this->status,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
        ];
    }
}
