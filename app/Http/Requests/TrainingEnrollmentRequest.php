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
        $enrollmentId = $this->route('training_enrollment')?->id;

        return [
            'end_user_id' => 'required|exists:end_users,id',
            'offer_id' => 'required|exists:training_offers,id',
            'status' => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'end_user_id.required' => 'End user is required',
            'end_user_id.exists' => 'Selected end user does not exist',
            'offer_id.required' => 'Training offer is required',
            'offer_id.exists' => 'Selected training offer does not exist',
            'status.in' => 'Status must be 0 or 1',
        ];
    }
}
