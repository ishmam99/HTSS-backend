<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrainerScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'training_course_id' => 'required|exists:training_courses,id',
            'days' => 'nullable|array|min:1',
            'days.*' => 'string',
            'status' => 'nullable|integer',
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
        ];
    }
}
