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
            'position_id' => 'required|exists:positions,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'job_type' => 'required|string',
            'location_type' => 'required|string',
            'base_country' => 'required|string',
            'required_experience' => 'required|string',
            'requirements' => 'required|array',
            'key_responsibilities' => 'required|array',
            'required_qualifications' => 'required|array',
            'key_skills' => 'required|array',
            'primary_software' => 'nullable|array',
            'deadline' => 'required|date',
            'number_of_vacancies' => 'nullable|integer',
            'salary_min' => 'nullable|numeric',
            'salary_max' => 'nullable|numeric',
            'status' => 'required|integer',
        ];
    }
}
