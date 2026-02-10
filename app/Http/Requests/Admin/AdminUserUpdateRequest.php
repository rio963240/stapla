<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AdminUserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_admin === true;
    }

    public function rules(): array
    {
        return [
            'is_admin' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
            'password' => ['nullable', 'string', Password::defaults()],
        ];
    }

    public function attributes(): array
    {
        return [
            'is_admin' => '権限',
            'is_active' => '状態',
            'password' => 'パスワード',
        ];
    }
}
