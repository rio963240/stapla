<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudyRecordStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'todo_id' => ['required', 'integer', 'exists:todo,todo_id'],
            'memo' => ['nullable', 'string', 'max:2000'],
            'records' => ['required', 'array', 'min:1'],
            'records.*.study_plan_items_id' => ['required', 'integer'],
            'records.*.actual_minutes' => ['required', 'integer', 'min:0', 'max:1440'],
        ];
    }
}
