<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'industry'  => $this->whenLoaded('industry'),
            'user' => new \App\Http\Resources\UserResource($this->whenLoaded('user')),
            'solutions' => $this->whenLoaded('solutions'),
            'softwares' => $this->whenLoaded('softwares'),
            'end_users' => $this->whenLoaded('endUsers'),
            'record_id' => $this->record_id,
            'tickets' =>CustomerSupportResource::collection($this->whenLoaded('tickets')),

        ];
    }
}
