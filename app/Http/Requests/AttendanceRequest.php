<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
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
            'date' => 'required|date',
            'status' => 'nullable|integer',
            'times' => 'required|array',
            'times.*.record_id' => 'required|exists:users,id',
            'times.*.type_of_work' => 'nullable|string',
            'times.*.notes' => 'nullable|string',
            'times.*.total_minute' => 'required|integer|min:1',
            'times.*.status' => 'nullable|integer',
            'times.*.attachment' => 'nullable|string',
        ];
    }
}
