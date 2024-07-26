<?php

namespace App\Http\Validators;

use Illuminate\Support\Facades\Validator;

class ProfitPerHourValidator
{
    public function validateGetByUserAnExchange($requestData): \Illuminate\Contracts\Validation\Validator
    {
        $commonRules = [
            'user_id' => 'required|integer|exists:users,id',
            'exchange_id' => 'required|integer|exists:exchanges,id',
        ];

        return Validator::make($requestData, $commonRules);
    }
}
