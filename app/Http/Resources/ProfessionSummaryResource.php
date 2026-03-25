<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfessionSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'user_id'             => $this->user_id,
            'summary'             => $this->summary,
            'experience_in_years' => $this->experience_in_years,
            'client_served'       => $this->client_served,
            'project_completed'   => $this->project_completed,
            'skills'              => $this->skills,
        ];
    }
}
