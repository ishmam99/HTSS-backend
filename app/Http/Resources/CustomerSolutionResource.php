<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerSolutionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'solution_name' => $this->solution?->name,
            'customer_name' => $this->customer?->load('usre')->name,
            'softwares' => $this->solution->softwares
        ];
    }
}
