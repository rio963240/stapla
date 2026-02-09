<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminDomainStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'qualification_id' => ['required', 'integer', 'exists:qualification,qualification_id'],
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
