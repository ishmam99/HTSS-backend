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
            'address' => $this->address,
            'marital_status' => $this->marital_status,
            'spouse_name' => $this->spouse_name,
            'spouse_number' => $this->spouse_number,
            'mother_name' => $this->mother_name,
            'father_name' => $this->father_name,
            'parent_name' => $this->parent_name,
            'parent_relation' => $this->parent_relation,
            'parent_phone_number' => $this->parent_phone_number,
            'siblings_name' => $this->siblings_name,
            'siblings_relation' => $this->siblings_relation,
            'siblings_phone_number' => $this->siblings_phone_number,

            // System info
            'system' => $this->system,
            'softwares' => $this->softwares,
            'industry_name' => $this->industry,
            'highest_education' => $this->highest_education,
            'university' => $this->university,

            // Resume & signature
            'resume' => $this->resume ? Storage::url($this->resume) : null,
            'signature_uploaded' => $this->signature_uploaded,
            'signature_path' => $this->signature_path ? Storage::url($this->signature_path) : null,
            'terms_accepted' => $this->terms_accepted,

            // Job/Software/Industry relations
            'job' => new JobResource($this->whenLoaded('job')),
            'software' => $this->software,
            'industry' => new IndustryResource($this->whenLoaded('industries')),

            // Evaluation / HR fields
            'technical_skills' => $this->technical_skills,
            'communication' => $this->communication,
            'cultural_fit' => $this->cultural_fit,
            'problem_solving' => $this->problem_solving,
            'overall_comment' => $this->overall_comment,
            'recommendation' => $this->recommendation,
            'expected_salary' => $this->expected_salary,

            // References
            'reference_one_name' => $this->reference_one_name,
            'reference_one_number' => $this->reference_one_number,
            'reference_one_designation' => $this->reference_one_designation,
            'reference_one_email' => $this->reference_one_email,

            'reference_two_name' => $this->reference_two_name,
            'reference_two_number' => $this->reference_two_number,
            'reference_two_designation' => $this->reference_two_designation,
            'reference_two_email' => $this->reference_two_email,

            // Verification
            'reference_checked' => $this->reference_checked,
            'background_verified' => $this->background_verified,
            'documents_verified' => $this->documents_verified,

            // Temporary access
            'access_token' => $this->access_token,
            'access_token_expires_at' => $this->access_token_expires_at,

            // Company Details
            'company_name' => $this->company_name,
            'company_email' => $this->company_email,
            'company_phone' => $this->company_phone,
            'experience_years' => $this->experience_years,

            // Status & timestamps
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}