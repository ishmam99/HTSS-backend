<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OnsiteSupportTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ticket_number' => 'nullable|string|max:255|unique:onsite_support_tickets,ticket_number,' . $this->route('onsite_support_ticket')?->id,
            'company_name' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'issue_type' => 'nullable|string|max:255',
            'priority_level' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'status' => 'nullable|integer',
        ];
    }
}