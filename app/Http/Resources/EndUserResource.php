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
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'customer_name' => $this->load('customer.user')->user->name,
            'industry_name' => $this->load('industry')->name,
            'customer_id' => $this->customer_id,
            'industry_id' => $this->industry_id,
            'softwares' => $this->softwares,
            'softwareLevels' => $this->softwareLevels->load('software')
        ];
    }
}
