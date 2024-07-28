<?php

namespace App\Http\Controllers;

use App\Http\Validators\ExchangeValidator;
use App\Models\Exchange;
use App\Models\ProfitPerHour;
use App\Traits\ResponseFormattingTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ExchangeController extends Controller
{
    use ResponseFormattingTrait;

    protected $exchangeValidator;

    /**
     * @param ExchangeValidator $exchangeValidator
     */
    public function __construct(ExchangeValidator $exchangeValidator)
    {
        $this->exchangeValidator = $exchangeValidator;
    }

    public function index(Request $request)
    {
        return Exchange::all();
    }

    public function getByUser(Request $request): array
    {
        try {
            $dataInput = $request->all();

            $validator = $this->exchangeValidator->validateGetByUser($dataInput);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $userId = $dataInput['user_id'];
            $exchange = ProfitPerHour::all()->where('user_id', $userId)
                ->where('is_active', '=', 1)
                ->first();

            return $this->_formatBaseResponse(200, $exchange, 'Success');

        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return $this->_formatBaseResponse(400, $errors, 'Failed');
        }
    }

}
