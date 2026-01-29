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
            'user_id'     => $this->whenLoaded('user', function () {
                return UserResource::make($this->user);
            }),
            'customer_id' => $this->whenLoaded('customer.user', function () {
                return CustomerResource::make($this->customer);
            }),
            'type'        => $this->type,
            'date'        => $this->date,
            'activity'    => $this->activity,
        ];
    }
}
