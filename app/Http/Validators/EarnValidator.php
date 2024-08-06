<?php

namespace App\Http\Validators;

use Illuminate\Support\Facades\Validator;

class EarnValidator
{
    public function validateGetByUser($requestData): \Illuminate\Contracts\Validation\Validator
    {
        $commonRules = [
            'user_id' => 'required|integer|exists:users,id',
        ];

        return Validator::make($requestData, $commonRules);
    }
}
