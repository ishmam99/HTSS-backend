<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrainerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($this->route('trainer')?->user_id ?? 'NULL'),
            'password' => 'nullable|string|min:8',
            'bio' => 'nullable|string',
            'expertise_area' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'linkedin_profile' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:255',
            'status' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.unique' => 'Email must be unique',
            'password.min' => 'Password must be at least 8 characters',
            'linkedin_profile.url' => 'LinkedIn profile must be a valid URL',
            'image.image' => 'Uploaded file must be an image',
            'image.mimes' => 'Image must be jpeg, png, jpg, gif, or webp',
            'image.max' => 'Image size must not exceed 2MB',
        ];
    }
}
