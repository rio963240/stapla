<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanRescheduleStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'target_id' => ['required', 'integer'],
            'reschedule_start_date' => ['required', 'date'],
            'daily_study_time' => ['required', 'integer', 'min:1', 'max:999'],
            'buffer_rate' => ['required', 'integer', 'min:0', 'max:99'],
            'no_study_days' => ['array'],
            'no_study_days.*' => ['date'],
            'weight_type' => ['required', 'string', 'in:domain,subdomain'],
            'weights' => ['required', 'array', 'min:1'],
            'weights.*.id' => ['required', 'integer'],
            'weights.*.weight' => ['required', 'integer', 'min:1', 'max:999'],
        ];
    }
}
