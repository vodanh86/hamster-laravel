<?php

namespace App\Http\Validators;

use Illuminate\Support\Facades\Validator;

class CardValidator
{
    public function validateGetByCategory($requestData): \Illuminate\Contracts\Validation\Validator
    {
        $commonRules = [
            'category_id' => 'required|integer|exists:category,id',
        ];

        return Validator::make($requestData, $commonRules);
    }

    public function validateGetAllWithCategory($requestData): \Illuminate\Contracts\Validation\Validator
    {
        $commonRules = [
            'user_id' => 'required|integer|exists:users,id',
            'exchange_id' => 'required|integer|exists:exchanges,id',
        ];

        return Validator::make($requestData, $commonRules);
    }
}
