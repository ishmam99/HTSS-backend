<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrainingCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

     public function rules(): array
    {
        $courseId = $this->route('training_course')?->id;
        return [
            'industry_id' => 'required|exists:industries,id',
            'customer_id' => 'nullable|exists:customers,id',
            'training_type' => 'nullable|string|max:255',
            'solution_id' => 'required|exists:solutions,id',
            'software_id' => 'required|exists:softwares,id',
            'training_level' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'course_id' => 'nullable|string',
            'course_code' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|string|max:100',
            'status' => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'industry_id.required' => 'Industry is required',
            'industry_id.exists' => 'Selected industry does not exist',
            'customer_id.exists' => 'Selected customer does not exist',
            'solution_name.required' => 'Solution name is required',
            'software_name.required' => 'Software name is required',
            'training_level.required' => 'Training level is required',
            'title.required' => 'Title is required',
            'course_id.unique' => 'Course ID must be unique',
        ];
    }
}
