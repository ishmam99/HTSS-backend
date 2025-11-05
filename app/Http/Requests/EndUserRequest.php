<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EndUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $endUserId = $this->route('end_user')?->id;
        return [
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($this->route('end_user')?->user_id ?? 'NULL'),
            'password' => 'nullable|string|min:8',
            'customer_id' => 'nullable|exists:customers,id',
            'industry_id' => 'nullable|exists:industries,id',
            'knowledge_level' => 'nullable|string|max:255',
            'status' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Username is required',
            'email.required' => 'Email is required',
            'email.unique' => 'Email must be unique',
            'password.min' => 'Password must be at least 8 characters',
            'customer_id.exists' => 'Selected customer does not exist',
            'industry_id.exists' => 'Selected industry does not exist',
            'image.image' => 'Uploaded file must be an image',
            'image.mimes' => 'Image must be jpeg, png, jpg, gif, or webp',
            'image.max' => 'Image size must not exceed 2MB',
        ];
    }
}
