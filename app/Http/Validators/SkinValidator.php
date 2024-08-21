<?php

namespace App\Http\Validators;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SkinValidator
{

    public function validateUserBuySkin($requestData): \Illuminate\Contracts\Validation\Validator
    {
        $commonRules = [
            'user_id' => 'required|integer|exists:users,id',
//            'skin_id' => 'required|integer|exists:skins,id',
            'skin_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if ($value !== -1 && !DB::table('skins')->where('id', $value)->exists()) {
                        $fail('The selected ' . $attribute . ' is invalid.');
                    }
                },
            ],
        ];

        return Validator::make($requestData, $commonRules);
    }
}
