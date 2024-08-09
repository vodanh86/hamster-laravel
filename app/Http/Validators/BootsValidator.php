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
            'current_user_boots_id' => 'required|integer|exists:user_boots,id',
            'current_boots_level' => 'required|integer',
            'next_user_boots_id' => 'required|integer|exists:user_boots,id',
            'next_boots_level' => 'required|integer',
            'type' => 'required|integer',
            'sub_type' => 'required|integer',
//            'is_completed' => 'required|integer'
        ];

        return Validator::make($requestData, $commonRules);
    }
}
