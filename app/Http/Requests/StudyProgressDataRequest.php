<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudyProgressDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'target_id' => ['required', 'integer'],
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date'],
        ];
    }
}
