<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerSupportResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'solution' => $this->whenLoaded('solution'),
            'software' => $this->whenLoaded('software'),
            'end_user' => $this->whenLoaded('endUser'),
            'customer' => $this->whenLoaded('customer'),
            'title' => $this->title,
            'description' => $this->description,
            'issue_type' => $this->issue_type,
            'priority_level' => $this->priority_level,
            'subject' => $this->subject,
            'attachment' => $this->attachment ? asset('storage/' . $this->attachment) : null,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'call_type' => $this->call_type,
            'priority' => $this->priority,
            'status' => $this->status,
            'record_call' => $this->record_call,
            'allow_guests' => $this->allow_guests,
            'send_reminders' => $this->send_reminders,
            'start_datetime' => $this->start_datetime,
            'duration_minutes' => $this->duration_minutes,
            'chat_type' => $this->chat_type,
            'allow_file_sharing' => $this->allow_file_sharing,
            'allow_anonymous' => $this->allow_anonymous,
            'ticket_number' => $this->ticket_number,
            'company_name' => $this->company_name,
            'location' => $this->location,

        ];
    }
}