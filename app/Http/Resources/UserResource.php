<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id'=> $this->id,
            'name'=> $this->name,
            'email'=> $this->email,
            'role'=> $this->role,

        ];
        if($this->role == 'customer')
        {
            $data['customer'] = CustomerResource::make($this->customer);
        }
        if($this->role == 'end-user')
        {
            $data['profile'] = EndUserResource::make($this->endUser);
        }
        if($this->role == 'trainer')
        {
            $data['trainer'] = TrainerResource::make($this->trainer);
        }
        return $data;
    }
}
