<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MeetingScheduleRequest extends FormRequest
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
            'meeting_title'          => 'required|string|max:255',
            'description'            => 'nullable|string',
            'date'                   => 'required|date',
            'duration'               => 'required|string',
            'time'                   => 'required',
            'timezone'               => 'required|string',
            'meeting_type'           => 'required|string',
            'meeting_link'           => 'nullable|string',
            'priority'               => 'required|string',
            'success_team_id'        => 'required|exists:success_teams,id',
            'success_team_user_id'   => 'required|array',
            'success_team_user_id.*' => 'exists:users,id',
            'type_of_activity'       => 'nullable|string',
            'location'               => 'nullable|string',
        ];
    }
}
