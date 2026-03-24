<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserEducationRequest extends FormRequest
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
            'institute_name' => 'required',
            'field_of_study' => 'required',
            'result'         => 'required',
            'start_year'     => 'required',
            'end_year'       => 'required',
            'status'         => 'nullable',
        ];
    }
}
