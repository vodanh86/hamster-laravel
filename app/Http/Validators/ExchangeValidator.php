<?php

namespace App\Http\Validators;

use Illuminate\Support\Facades\Validator;

class ExchangeValidator
{
    public function validateGetByUser($requestData): \Illuminate\Contracts\Validation\Validator
    {
        $commonRules = [
            'user_id' => 'required|integer|exists:users,id',
        ];

        return Validator::make($requestData, $commonRules);
    }
    public function validateUpdateExchangeByUser($requestData): \Illuminate\Contracts\Validation\Validator
    {
        $commonRules = [
            'user_id' => 'required|integer|exists:users,id',
            'exchange_id' => 'required|integer|exists:exchanges,id',
        ];

        return Validator::make($requestData, $commonRules);
    }
}
