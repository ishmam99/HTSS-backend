<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EndUserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'knowledge_level' => $this->knowledge_level,
            'status' => $this->status,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'secondary_email' => $this->secondary_email,
            'state' => $this->state,
            'zip_code' => $this->zip_code,
            'city' => $this->city,
            'country' => $this->country,
            'direct_phone' => $this->direct_phone,
            'cell_phone' => $this->cell_phone,
            'department' => $this->department,
            'discipline' => $this->discipline,
            'current_industry' => $this->current_industry,
            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'email' => $this->user?->email,
            ],
            'customer_name' => $this->load('customer.user')->customer?->user->name,
            'industry_name' => $this->load('industry')->industry?->name,
            'customer_id' => $this->customer_id,
            'industry_id' => $this->industry_id,
            'softwares' => $this->softwares,
            'softwareLevels' => $this->softwareLevels->load('software')
        ];
    }
}
