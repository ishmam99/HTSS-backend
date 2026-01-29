<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MonthlyCSMActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'user'     => $this->whenLoaded('user', function () {
                return UserResource::make($this->user);
            }),
            'customer' => $this->whenLoaded('customer', function () {
                return CustomerResource::make($this->customer);
            }),
            'type'        => $this->type,
            'date'        => $this->date,
            'activity'    => $this->activity,
        ];
    }
}
