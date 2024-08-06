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

    public function validateUpdateEarnByUser($requestData): \Illuminate\Contracts\Validation\Validator
    {
        $commonRules = [
            'user_id' => 'required|integer|exists:users,id',
            'user_earn_id' => 'required|integer|exists:user_earn,id',
//            'is_completed' => 'required|integer'
        ];

        return Validator::make($requestData, $commonRules);
    }
}
