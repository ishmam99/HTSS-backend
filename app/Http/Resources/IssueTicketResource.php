<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IssueTicketResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'issue_type' => $this->issue_type,
            'priority_level' => $this->priority_level,
            'subject' => $this->subject,
            'description' => $this->description,
            'attachment' => $this->attachment ? asset('storage/' . $this->attachment) : null,
            'status' => $this->status,
            "user" => new UserResource($this->whenLoaded('user')),
        ];
    }
}
