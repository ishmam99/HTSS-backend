<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'forwarding_letter' => 'required|string|max:255',
            'deal_name' => 'required|string|max:255',
            'software_area' => 'nullable|string|max:255',
            'software_name' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'service_type' => 'nullable|string|max:255',
            'proposal_amount' => 'nullable|numeric',
            'terms_and_conditions' => 'nullable|string',
            'special_terms_and_conditions' => 'nullable|string',
            'status' => 'nullable|integer',
            'account_id' => 'nullable|exists:records,id',
            'deal_id' => 'nullable|exists:records,id',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
        ];
    }
}