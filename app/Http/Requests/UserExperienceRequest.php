<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserExperienceRequest extends FormRequest
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
            'company_name'     => 'required',
            'position'         => 'required',
            'start_date'       => 'required',
            'is_current'       => 'nullable|boolean',
            'end_date'         => 'nullable',
            'responsibilities' => 'nullable',
            'location'         => 'nullable',
            'status'           => 'nullable',
        ];
    }
}
