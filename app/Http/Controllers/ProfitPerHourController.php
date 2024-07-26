<?php

namespace App\Http\Controllers;

use App\Http\Validators\ProfitPerHourValidator;
use App\Models\User;
use App\Traits\ResponseFormattingTrait;
use Carbon\Carbon;
use App\Models\ProfitPerHour;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProfitPerHourController extends Controller
{
    use ResponseFormattingTrait;

    protected $profitPerHourValidator;

    /**
     * @param ProfitPerHourValidator $profitPerHourValidator
     */
    public function __construct(ProfitPerHourValidator $profitPerHourValidator)
    {
        $this->profitPerHourValidator = $profitPerHourValidator;
    }

    public function index(Request $request)
    {
        return ProfitPerHour::all();
    }

    public function getByUserAndExchange(Request $request): array
    {
        try {
            $dataInput = $request->all();

            $validator = $this->profitPerHourValidator->validateGetByUserAnExchange($dataInput);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $userId = $dataInput['user_id'];
            $exchangeId = $dataInput['exchange_id'];
            $profitPerHour = ProfitPerHour::all()
                ->where('user_id', '=', $userId)
                ->where('exchange_id', '=', $exchangeId)->all();

            return $this->_formatBaseResponse(200, $profitPerHour, 'Success');

        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return $this->_formatBaseResponse(400, $errors, 'Failed');
        }
    }

}
