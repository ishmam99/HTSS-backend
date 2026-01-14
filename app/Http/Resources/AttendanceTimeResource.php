<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CRM\Models\RecordValue;

class AttendanceTimeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type_of_work' => $this->type_of_work,
            'notes' => $this->notes,
            'total_minute' => $this->total_minute,
            'activity' => $this->activity,

            'task_name'=> $this->task_name,
            'description' => $this->description,
            'output'=> $this->output,
            'total_hours' => number_format($this->total_minute/60, 2),
            'status' => $this->status,
            'attachment' => $this->attachment
                ? asset('storage/' . $this->attachment)
                : null,
            'account' => $this->record ? [
                'id' => $this->record->id,
                'name' => RecordValue::where('record_id',$this->record_id)->where('field_id',2)->first()?->value

            ] : null,
        ];
    }
}