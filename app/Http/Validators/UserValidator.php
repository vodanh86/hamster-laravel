<?php

namespace App\Http\Validators;

use Illuminate\Support\Facades\Validator;

class UserValidator
{
    public function validateLogin($requestData): \Illuminate\Contracts\Validation\Validator
    {
        $commonRules = [
            'telegram_id' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'username' => 'required|string',
            'language_code' => 'required|string',
        ];

        return Validator::make($requestData, $commonRules);
    }
}
