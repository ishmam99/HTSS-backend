<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuccessTeamActivityReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // use policy later if needed
    }

    public function rules(): array
    {
        return [
            'success_team_id'    => 'required|exists:success_teams,id',
            'period'             => [
                'required',
                'regex:/^(January|February|March|April|May|June|July|August|September|October|November|December)-\d{4}$/',
            ],
            'status'             => 'nullable|integer|in:0,1,2',
            'summary_activities' => 'nullable|array',
            'key_outcomes'       => 'nullable|array',
        ];
    }
}
