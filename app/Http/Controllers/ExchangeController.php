<?php

namespace App\Http\Controllers;

use App\Admin\Controllers\ConstantHelper;
use App\Http\Validators\ExchangeValidator;
use App\Models\Exchange;
use App\Models\ProfitPerHour;
use App\Traits\ResponseFormattingTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

            $result= DB::table('profit_per_hours as pp')
                ->select('pp.id' , 'pp.user_id', 'pp.profit_per_hour','pp.exchange_id','is_active','ex.name','ex.description','ex.image')
                ->join('exchanges as ex', 'ex.id', '=', 'pp.exchange_id')
                ->where('pp.is_active', '=', ConstantHelper::STATUS_ACTIVE)
                ->where('pp.user_id', '=', $userId)
                ->get();

            return $this->_formatBaseResponse(200, $result, 'Success');

        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return $this->_formatBaseResponse(400, $errors, 'Failed');
        }
    }

 public function updateExchangeByUser(Request $request): array
    {
        try {
            $dataInput = $request->all();

            $validator = $this->exchangeValidator->validateUpdateExchangeByUser($dataInput);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $userId = $dataInput['user_id'];
            $exchangeId = $dataInput['exchange_id'];

            //deactive exchange cu
            $currentExchange=ProfitPerHour::all()
                ->where('user_id','=', $userId)
                ->where('is_active','=', ConstantHelper::STATUS_ACTIVE)
                ->first();

            if($currentExchange){
                $currentExchange->is_active = ConstantHelper::STATUS_IN_ACTIVE;
                $currentExchange->update();
            }

            //active exchange moi
            $exchange=ProfitPerHour::all()
                ->where('user_id','=', $userId)
                ->where('exchange_id','=', $exchangeId)
                ->first();

            if($exchange){
                $exchange->is_active = ConstantHelper::STATUS_ACTIVE;
                $exchange->update();
            }


            return $this->_formatBaseResponse(200, $exchange, 'Success');

        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return $this->_formatBaseResponse(400, $errors, 'Failed');
        }
    }

}
