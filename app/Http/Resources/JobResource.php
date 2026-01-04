<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
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
            'department' => $this->department->name ?? null,
            'title' => $this->title,
            'overview' => $this->overview,
            'job_type' => $this->job_type,
            'location_type' => $this->location_type,
            'base_country' => $this->base_country,
            'required_experience' => $this->required_experience,
            'key_responsibilities' => json_decode($this->key_responsibilities, true),
            'required_qualifications' => json_decode($this->required_qualifications, true),
            'key_skills' => json_decode($this->key_skills, true),
            'primary_software' => json_decode($this->primary_software, true),
            'deadline' => $this->deadline,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
