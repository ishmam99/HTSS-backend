<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrainerRequestFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'industry_id' => 'nullable|exists:industries,id',
            'solution_id' => 'nullable|exists:solutions,id',
            'software_id' => 'nullable|exists:softwares,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:trainer_request_forms,email',
            'phone' => 'required|string|unique:trainer_request_forms,phone',
            'address' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'experience_year' => 'nullable|string'
        ];
    }
}
