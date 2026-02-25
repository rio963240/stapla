<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param  array<string, string>  $input
     */
    public function reset(User $user, array $input): void
    {
        // パスワードのバリデーション
        Validator::make($input, [
            'password' => $this->passwordRules(),
        ])->validate();

        // 新しいパスワードを保存
        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
