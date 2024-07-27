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
}
