<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerSuccessManagerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'user'       => $this->whenLoaded('user'),
            'phone'      => $this->phone,
            'address'    => $this->address,
            'city'       => $this->city,
            'country'    => $this->country,
            'postal_code'=> $this->postal_code,
            'date_of_birth'=> $this->date_of_birth,
            'gender'     => $this->gender,
            'status'     => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'customers' => $this->customers->map(function($customer) {
                return [
                    'id'   => $customer->id,
                    'name' => $customer->name,
                ];
            }),
        ];
    }
}

