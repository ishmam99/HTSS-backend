<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\JobResource;
use App\Http\Resources\SoftwareResource;
use App\Http\Resources\IndustryResource;

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
            'system' => $this->system,
            'softwares' => $this->softwares,
         'industry' => new IndustryResource($this->whenLoaded('industry')),
        
            'highest_education' => $this->highest_education,
            'university' => $this->university,
            'resume' => $this->resume ? Storage::url($this->resume) : null,
            'job_id' => $this->job_id,
            'software_id' => $this->software_id,
            'industry_id' => $this->industry_id,
            'job' => new JobResource($this->whenLoaded('job')), // Include job data when loaded
            'software' =>$this->software,
            'industry' => new IndustryResource($this->whenLoaded('industry')), // Include industry data when loaded
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
