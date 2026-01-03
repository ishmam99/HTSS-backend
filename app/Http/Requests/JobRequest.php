<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'department_id' => 'required|exists:departments,id',
            'title' => 'required|string|max:255',
            'overview' => 'required|string',
            'job_type' => 'required|string',
            'location_type' => 'required|string',
            'base_country' => 'required|string',
            'required_experience' => 'required|string',
            'key_responsibilities' => 'required|array',
            'required_qualifications' => 'required|array',
            'key_skills' => 'required|array',
            'primary_software' => 'required|array',
            'deadline' => 'required|date',
            'status' => 'required|integer',
        ];
    }
}
