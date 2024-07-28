<?php

namespace App\Http\Controllers;

use App\Admin\Controllers\UtilsQueryHelper;
use App\Http\Validators\UserCardValidator;
use App\Models\Category;
use App\Models\UserCard;
use App\Traits\ResponseFormattingTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserCardController extends Controller
{
    use ResponseFormattingTrait;

    protected $userCardValidator;

    /**
     * @param UserCardValidator $userCardValidator
     */
    public function __construct(UserCardValidator $userCardValidator)
    {
        $this->userCardValidator = $userCardValidator;
    }

    public function index(Request $request)
    {
        $data = UserCard::all();
        return $this->_formatBaseResponse(200, $data, 'Success');
    }

    public function store(Request $request): array
    {
        try {
            $dataInput = $request->all();

            $validator = $this->userCardValidator->validateBuyCard($dataInput);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $userCard = new UserCard();
            $userCard->user_id = $dataInput["user_id"];
            $userCard->card_id = $dataInput["card_id"];
            $userCard->level = $dataInput["level"];
            $userCard->card_profit_id = $dataInput["card_profit_id"];
            $userCard->exchange_id = $dataInput["exchange_id"];

            //level
//            $cardProfit = (new UtilsQueryHelper())::findCardProfitById($userCard->car_profit_id);
            $userCard->created_at = now()->toDateTime();
            $userCard->save();

            return $this->_formatBaseResponse(201, $userCard, 'Success');
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return $this->_formatBaseResponse(400, $errors, 'Failed');
        }
    }

}
