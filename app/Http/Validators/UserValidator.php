<?php

namespace App\Http\Validators;

use Illuminate\Support\Facades\Validator;

class UserValidator
{
    public function validateLogin($requestData): \Illuminate\Contracts\Validation\Validator
    {
        $commonRules = [
            'telegram_id' => 'required|string',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'username' => 'nullable|string',
            'reference_telegram_id' => 'nullable|string',
            'language_code' => 'required|string',
        ];

        return Validator::make($requestData, $commonRules);
    }

    public function validateUpdateRevenue($requestData): \Illuminate\Contracts\Validation\Validator
    {
        $commonRules = [
            'user_id' => 'required|integer|exists:users,id',
            'amount' => 'required|integer',
        ];

        return Validator::make($requestData, $commonRules);
    }

    public function validateUpdateSkin($requestData): \Illuminate\Contracts\Validation\Validator
    {
        $commonRules = [
            'user_id' => 'required|integer|exists:users,id',
            'skin_id' => 'required|integer|exists:skins,id',
        ];

        return Validator::make($requestData, $commonRules);
    }

    public function validateGetRankByMembership($requestData): \Illuminate\Contracts\Validation\Validator
    {
        $commonRules = [
            'user_id' => 'required|integer|exists:users,id',
        ];

        return Validator::make($requestData, $commonRules);
    }
}
