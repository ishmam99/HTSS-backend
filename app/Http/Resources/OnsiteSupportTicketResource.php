<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OnsiteSupportTicketResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'ticket_number' => $this->ticket_number,
            'company_name' => $this->company_name,
            'location' => $this->location,
            'issue_type' => $this->issue_type,
            'priority_level' => $this->priority_level,
            'subject' => $this->subject,
            'description' => $this->description,
            'attachment' => $this->attachment ? asset('storage/' . $this->attachment) : null,
            'status' => $this->status,
        ];
    }
}
