<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResumeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'resume'     => $this->resume,
            'template'   => $this->template,
            'status'     => $this->status,
            'end_user'   => $this->endUser,
            'created_at' => $this->created_at,

        ];
    }
}
