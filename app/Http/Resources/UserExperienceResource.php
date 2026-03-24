<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserExperienceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'user_id'          => $this->user_id,
            'company_name'     => $this->company_name,
            'position'         => $this->position,
            'start_date'       => $this->start_date,
            'is_current'       => $this->is_current,
            'end_date'         => $this->end_date,
            'responsibilities' => $this->responsibilities,
            'location'         => $this->location,
            'status'           => $this->status,
        ];
    }
}
