<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrainingOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $offerId = $this->route('training_offer')?->id;

        return [
            'training_event_id' => 'required|exists:training_events,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'available_seats' => 'nullable|integer|min:0',
            'status' => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'training_event_id.required' => 'Training event is required',
            'training_event_id.exists' => 'Selected training event does not exist',
            'start_date.required' => 'Start date is required',
            'start_date.date' => 'Start date must be a valid date',
            'end_date.date' => 'End date must be a valid date',
            'end_date.after_or_equal' => 'End date must be after or equal to start date',
            'price.numeric' => 'Price must be a numeric value',
            'price.min' => 'Price cannot be negative',
            'available_seats.integer' => 'Available seats must be an integer',
            'available_seats.min' => 'Available seats cannot be negative',
        ];
    }
}
