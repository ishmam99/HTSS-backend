<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AppliedJobResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,

            // Basic Info
            'full_name'         => $this->full_name,
            'email'             => $this->email,
            'contact'           => $this->contact,
            'emergency_contact' => $this->emergency_contact,
            'address'           => $this->address,

            // Personal Info
            'marital_status'        => $this->marital_status,
            'spouse_name'           => $this->spouse_name,
            'spouse_number'         => $this->spouse_number,
            'father_name'           => $this->father_name,
            'mother_name'           => $this->mother_name,
            'parent_name'           => $this->parent_name,
            'parent_relation'       => $this->parent_relation,
            'parent_phone_number'   => $this->parent_phone_number,
            'siblings_name'         => $this->siblings_name,
            'siblings_relation'     => $this->siblings_relation,
            'siblings_phone_number' => $this->siblings_phone_number,

            // Education & Work
            'highest_education' => $this->highest_education,
            'university'        => $this->university,
            'experience_years'  => $this->experience_years,
            'company_name'      => $this->company_name,
            'company_phone'     => $this->company_phone,
            'company_email'     => $this->company_email,

            // Skills / System Info (plain string columns on the model)
            'system'    => $this->system,
            'softwares' => $this->softwares,
            'industry'  => $this->industry,

            // Resume & Links
            'resume' => $this->resume ? Storage::url($this->resume) : null,
            'link'   => $this->link,

            // Signature
            'signature_uploaded' => $this->signature_uploaded,
            'signature_path'     => $this->signature_path ? Storage::url($this->signature_path) : null,

            // Terms
            'terms_accepted' => $this->terms_accepted,

            // Relations (singular — belongsTo)
            'job'      => new JobResource($this->whenLoaded('job')),
            'software' => $this->whenLoaded('software'),
            'industry' => new IndustryResource($this->whenLoaded('industry')),

            // Salary
            'expected_salary'   => $this->expected_salary,
            'negotiated_salary' => $this->negotiated_salary,

            // HR Evaluation / Scoring
            'technical_skills' => $this->technical_skills,
            'communication'    => $this->communication,
            'cultural_fit'     => $this->cultural_fit,
            'problem_solving'  => $this->problem_solving,
            'overall_comment'  => $this->overall_comment,
            'recommendation'   => $this->recommendation,

            // References
            'reference_one_name'        => $this->reference_one_name,
            'reference_one_number'      => $this->reference_one_number,
            'reference_one_designation' => $this->reference_one_designation,
            'reference_one_email'       => $this->reference_one_email,
            'reference_two_name'        => $this->reference_two_name,
            'reference_two_number'      => $this->reference_two_number,
            'reference_two_designation' => $this->reference_two_designation,
            'reference_two_email'       => $this->reference_two_email,

            // HR Verification Flags
            'reference_checked'   => $this->reference_checked,
            'background_verified' =>  $this->background_verified,
            'documents_verified'  =>  $this->documents_verified,
            'educational_background_check'      => $this->educational_background_check,
            'professional_background_check'     => $this->professional_background_check,
            'experience_background_check'     => $this->experience_background_check,

            'educational_background_check_document' => $this->educational_background_check_document ? Storage::url($this->educational_background_check_document) : null,
            'experience_background_check_document'  => $this->experience_background_check_document ? Storage::url($this->experience_background_check_document) : null,
            'police_background_check_document'      => $this->police_background_check_document ? Storage::url($this->police_background_check_document) : null,

            // Offer Letter Fields
            'responsibilities'  => $this->responsibilities,
            'benefits'          => $this->benefits,
            'employment_terms'  => $this->employment_terms,
            'terms_clauses'     => $this->terms_clauses,
            'joining_date'      => $this->joining_date,
            'offering_date'     => $this->offering_date,
            'completed_at'      => $this->completed_at,

            // Temporary Access
            'access_token'            => $this->access_token,
            'access_token_expires_at' => $this->access_token_expires_at,

            // Status & Timestamps
            'status'     => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}