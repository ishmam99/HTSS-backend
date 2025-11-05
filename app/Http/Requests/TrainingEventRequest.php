<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrainingEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $eventId = $this->route('training_event')?->id;

        return [
            'training_course_id' => 'required|exists:training_courses,id',
            'training_type' => 'nullable|string|max:255',
            'platform' => 'nullable|string|max:255',
            'trainer_id' => 'nullable|exists:trainers,id',
            'status' => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'training_course_id.required' => 'Course is required',
            'training_course_id.exists' => 'Selected course does not exist',
            'trainer_id.exists' => 'Selected trainer does not exist',
        ];
    }
}