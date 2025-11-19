<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndustryResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'sector_code' => $this->sector_code,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'solutions' =>$this->whenLoaded('solutions'),
            'softwares' =>$this->whenLoaded('softwares'),
            'trainings' =>$this->whenLoaded('trainings'),
            'customers' =>CustomerResource::collection($this->whenLoaded('customers')),
        ];
    }
}
