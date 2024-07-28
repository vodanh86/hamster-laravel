<?php

namespace App\Http\Controllers;

use App\Http\Validators\UserCardValidator;
use App\Models\UserCard;
use App\Traits\ResponseFormattingTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function getCardsByUserIdAndCategoryId(Request  $request): array
    {

        try{
            $dataInput = $request->all();

            $validator = $this->userCardValidator->validateGetByUserAndCategoryAndExchange($dataInput);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $userId = $dataInput["user_id"];
            $exchangeId = $dataInput["exchange_id"];
            $categoryId = $dataInput["category_id"];

            $subQuery = DB::table('user_card as uc')
                ->select('uc.card_id', DB::raw('MAX(uc.level) as max_level'))
                ->where('uc.user_id', $userId)
                ->groupBy('uc.card_id');

            $userCards = UserCard::join('card', 'user_card.card_id', '=', 'card.id')
                ->join('card_profit', 'user_card.card_profit_id', '=', 'card_profit.id')
                ->joinSub($subQuery, 'max_levels', function ($join) {
                    $join->on('user_card.card_id', '=', 'max_levels.card_id')
                        ->on('user_card.level', '=', 'max_levels.max_level');
                })
                ->where('user_card.user_id', $userId)
                ->where('user_card.exchange_id', $exchangeId)
                ->where('card.category_id', $categoryId)
                ->with(['card', 'cardProfit', 'exchange'])
                ->orderBy('card.order', 'asc')
                ->get();

            return $this->_formatBaseResponse(200, $userCards, 'Success');
        }catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return $this->_formatBaseResponse(400, $errors, 'Failed');
        }

    }

}
