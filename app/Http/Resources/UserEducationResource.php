<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserEducationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'user_id'        => $this->user_id,
            'institute_name' => $this->institute_name,
            'field_of_study' => $this->field_of_study,
            'result'         => $this->result,
            'start_year'     => $this->start_year,
            'end_year'       => $this->end_year,
            'status'         => $this->status,
        ];
    }
}
