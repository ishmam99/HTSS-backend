<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrainingEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'end_user_id' => 'required|exists:end_users,id',
            'training_request_id' => 'nullable|exists:training_requests,id',
            'training_course_schedule_id' => 'nullable|exists:training_course_schedules,id',
            'status' => 'nullable|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'end_user_id.required' => 'End user is required',
            'end_user_id.exists' => 'Selected end user does not exist',
            'training_request_id.exists' => 'Selected training request does not exist',
            'training_course_schedule_id.exists' => 'Selected training course schedule does not exist',
            'status.in' => 'Status must be 0 or 1',
        ];
    }
}
