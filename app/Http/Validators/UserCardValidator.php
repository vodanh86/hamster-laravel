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
            'level' => 'required|integer|gt:1',
            'card_profit_id' => 'required|integer|exists:card_profit,id',
            'exchange_id' => 'required|integer|exists:exchanges,id',
        ];

        return Validator::make($requestData, $commonRules);
    }

    public function validateGetByUserAndCategoryAndExchange($requestData): \Illuminate\Contracts\Validation\Validator
    {
        $commonRules = [
            'user_id' => 'required|integer|exists:users,id',
            'category_id' => 'required|integer|exists:category,id',
            'exchange_id' => 'required|integer|exists:exchanges,id',
        ];

        return Validator::make($requestData, $commonRules);
    }

}
