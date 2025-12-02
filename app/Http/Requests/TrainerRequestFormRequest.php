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

            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:trainer_request_forms,email',
            'phone' => 'required|string|unique:trainer_request_forms,phone',
            'address' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'experience_year' => 'nullable|string',
            'current_company'   => 'nullable|string',
            'current_position'  => 'nullable|string',
            'status' => 'nullable'
        ];
    }
}
