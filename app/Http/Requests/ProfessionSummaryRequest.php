<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfessionSummaryRequest extends FormRequest
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
            'summary'             => 'required|string',
            'experience_in_years' => 'required|string',
            'client_served'       => 'required|string',
            'project_completed'   => 'required|string',
            'skills'              => 'required|array',
            'skills.*'            => 'string',
        ];
    }
}
