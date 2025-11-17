<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerSupportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'end_user_id'       => 'required|exists:end_users,id',
            'customer_id'       => 'required|exists:customers,id',
            'type'               => 'required|string',
            'solution_id'        => 'nullable|exists:solutions,id',
            'software_id'        => 'nullable|exists:softwares,id',
            'title'              => 'nullable|string',
            'description'        => 'nullable|string',
            'issue_type'         => 'nullable|string',
            'priority_level'     => 'nullable|string',
            'subject'            => 'nullable|string',

            'attachment'         => 'nullable',

            'date'               => 'nullable|date',
            'start_time'         => 'nullable',
            'end_time'           => 'nullable',

            'call_type'          => 'nullable|string',
            'priority'           => 'nullable|string',

            'status'             => 'nullable|integer',
            'record_call'        => 'nullable|integer',
            'allow_guests'       => 'nullable|integer',
            'send_reminders'     => 'nullable|integer',

            'start_datetime'     => 'nullable|date',
            'duration_minutes'   => 'nullable|integer',

            'chat_type'          => 'nullable|string',

            'allow_file_sharing' => 'nullable|integer',
            'allow_anonymous'    => 'nullable|integer',

            'ticket_number'      => 'nullable|string',
            'company_name'       => 'nullable|string',
            'location'           => 'nullable|string',
        ];
    }
}
