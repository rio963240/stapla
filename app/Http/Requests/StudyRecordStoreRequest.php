<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $records = $this->input('records', []);
            if (!is_array($records) || $records === []) {
                return;
            }

            $total = 0;
            foreach ($records as $record) {
                if (!is_array($record)) {
                    continue;
                }
                $minutes = (int) ($record['actual_minutes'] ?? 0);
                $total += $minutes;
            }

            if ($total > 1440) {
                $validator->errors()->add('records', '1日の実績合計は1440分（24時間）までです。');
            }
        });
    }
}
