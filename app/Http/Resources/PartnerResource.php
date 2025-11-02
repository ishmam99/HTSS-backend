<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'phone' => $this->phone,
            'company_name' => $this->company_name,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'website' => $this->website,
            'partner_type' => $this->partner_type,
            'gender' => $this->gender,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new \App\Http\Resources\UserResource($this->whenLoaded('user')),
        ];
    }
}
