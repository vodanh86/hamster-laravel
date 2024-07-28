<?php

namespace App\Http\Validators;

use Illuminate\Support\Facades\Validator;

class UserCardValidator
{
    public function validateBuyCard($requestData): \Illuminate\Contracts\Validation\Validator
    {
        $commonRules = [
            'user_id' => 'required|integer|exists:users,id',
            'card_id' => 'required|integer|exists:card,id',
            'level' => 'required|integer',
            'card_profit_id' => 'required|integer|exists:card_profit,id',
            'exchange_id' => 'required|integer|exists:exchanges,id',
        ];

        return Validator::make($requestData, $commonRules);
    }

}
