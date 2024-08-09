<?php

namespace App\Http\Validators;

use Illuminate\Support\Facades\Validator;

class BootsValidator
{
    public function validateGetByUser($requestData): \Illuminate\Contracts\Validation\Validator
    {
        $commonRules = [
            'user_id' => 'required|integer|exists:users,id',
        ];

        return Validator::make($requestData, $commonRules);
    }

    public function validateUpdateBootsByUser($requestData): \Illuminate\Contracts\Validation\Validator
    {
        $commonRules = [
            'user_id' => 'required|integer|exists:users,id',
            'user_boots_id' => 'required|integer|exists:user_boots,id',
//            'is_completed' => 'required|integer'
        ];

        return Validator::make($requestData, $commonRules);
    }
}
