<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SoftwareLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.industry_id' => 'nullable|exists:industries,id',
            'items.*.solution_id' => 'nullable|exists:solutions,id',
            'items.*.software_id' => 'nullable|exists:softwares,id',
            'items.*.levels' => 'required|string',
            'items.*.status' => 'nullable|integer',
        ];
    }
}
