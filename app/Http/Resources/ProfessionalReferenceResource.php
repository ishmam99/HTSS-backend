<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfessionalReferenceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'designation' => $this->designation,
            'company_name' => $this->company_name,
            'email'       => $this->email,
            'phone'       => $this->phone,
            'note'        => $this->note,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'created_at'  => $this->created_at,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id'   => $this->user->id,
                    'name' => $this->user->name,
                    'role' => $this->user->role,
                ];
            }),
        ];
    }
}
