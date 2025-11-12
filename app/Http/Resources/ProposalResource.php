<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProposalResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'forwarding_letter' => $this->forwarding_letter,
            'deal_name' => $this->deal_name,
            'software_area' => $this->software_area,
            'software_name' => $this->software_name,
            'industry' => $this->industry,
            'service_type' => $this->service_type,
            'proposal_amount' => $this->proposal_amount,
            'terms_and_conditions' => json_decode($this->terms_and_conditions, true),
            'special_terms_and_conditions' => json_decode($this->special_terms_and_conditions, true),
            'attachment' => $this->attachment ? asset('storage/' . $this->attachment) : null,
            'status' => $this->status,
            'account' => $this->whenLoaded('account'),
            'deal' => $this->whenLoaded('deal'),
            'created_by' => $this->whenLoaded('createdBy'),
            'updated_by' => $this->whenLoaded('updatedBy'),
        ];
    }
}