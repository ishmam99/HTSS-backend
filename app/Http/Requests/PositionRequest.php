<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use function PHPUnit\Framework\isTrue;

class PositionRequest extends FormRequest
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
            'department_id' => $this->isMethod('post')
                ? 'required|exists:departments,id'
                : 'sometimes|required|exists:departments,id',

            'title' => $this->isMethod('post')
                ? 'required|string|max:255'
                : 'sometimes|required|string|max:255',

            'level' => $this->isMethod('post')
                ? 'required|string|max:100'
                : 'sometimes|required|string|max:100',

            'employment_type' => $this->isMethod('post')
                ? 'required|string|max:100'
                : 'sometimes|required|string|max:100',

            'description' => 'nullable|string',
            'status' => 'nullable|integer',
        ];
    }
}
