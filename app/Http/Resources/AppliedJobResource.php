<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AppliedJobResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'contact' => $this->contact,
            'emergency_contact' => $this->emergency_contact,

            // system info
            'system' => $this->system,

            // string column (not relation)
            'softwares' => $this->softwares,

            // string column (not relation)
            'industry_name' => $this->industry,

            'highest_education' => $this->highest_education,
            'university' => $this->university,

            'resume' => $this->resume
                ? Storage::url($this->resume)
                : null,

            // ✅ relations (ALWAYS use Resource)
            'job' => new JobResource($this->whenLoaded('job')),
            'software' =>$this->software,
            // 'industry' => new IndustryResource($this->whenLoaded('industry')),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
