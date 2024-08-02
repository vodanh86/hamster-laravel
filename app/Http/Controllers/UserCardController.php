<?php

namespace App\Http\Controllers;

use App\Admin\Controllers\UtilsQueryHelper;
use App\Http\Validators\UserCardValidator;
use App\Models\User;
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
            $cardId = $dataInput["card_id"];
            $cardProfitId = $dataInput["card_profit_id"];
            $cardlevel = $dataInput["level"];
            $userId = $dataInput["user_id"];;
            $exchangeId = $dataInput["exchange_id"];

            //lay thong tin user_card hien tai
            //TODO: lay profit cua card hien tai - profit cua card hien tại , sau do cong vao profitPerHour, update revenue
            $currentCardProfit = (new UtilsQueryHelper())::findPrevCardProfitByCardAndCardProfit($cardId, $cardProfitId);
            if (!$currentCardProfit) {
                return $this->_formatBaseResponse(400, null, 'Error with current card');
            }
            $currentProfit = $currentCardProfit->profit;
//            error_log('$currentProfit: ' . $currentProfit);

            //next level
            $nextCardProfit = (new UtilsQueryHelper())::findCardProfitByCardAndLevel($cardId, $cardlevel);
            if (!$nextCardProfit) {
                return $this->_formatBaseResponse(400, null, 'Card and Level is not exist!');
            }
            $nextProfit = $nextCardProfit->profit;
//            error_log('$nextProfit: ' . $nextProfit);
            $increaseProfit = $nextProfit - $currentProfit;
//            error_log('$increaseProfit: ' . $increaseProfit);

            $userCard = new UserCard();
            $userCard->user_id = $userId;
            $userCard->card_id = $cardId;
            $userCard->level = $cardlevel;
            $userCard->card_profit_id = $cardProfitId;
            $userCard->exchange_id = $exchangeId;
//
//            //level
            $userCard->created_at = now()->toDateTime();
//            $userCard->save();

            //update profit per hour
            $profitPerHour = (new UtilsQueryHelper())::findProfitPerHourByUserAndExchange($userId, $exchangeId);
            if (!$profitPerHour) {
                return $this->_formatBaseResponse(400, null, 'Exchange is not active!');
            }
            $currentProfitPerHour = $profitPerHour->profit_per_hour;
//            error_log('$currentProfitPerHour: ' . $currentProfitPerHour);
            $nextProfitPerHour = $currentProfitPerHour + $increaseProfit;
//            error_log('$nextProfitPerHour: ' . $nextProfitPerHour);
            $profitPerHour->profit_per_hour = $nextProfitPerHour;
//            $profitPerHour->update();


            //update revenue
            $user = User::findOrFail($userId);
            if ($user) {
                //get current value
                $currentRevenue = (int)$user->revenue;
//                error_log('$currentRevenue: ' . $currentRevenue);
                $newRevenue = $currentRevenue - $increaseProfit;
//                error_log('$newRevenue: ' . $newRevenue);
                $user->revenue = $newRevenue;

//                $user->update();
            }


            $categoryList = (new UtilsQueryHelper())::listCardByUserAndExchange($userId, $exchangeId);

            $updatedUser=[
                'profitPerHour' => $profitPerHour,
                'revenue' => $user->revenue
            ];

            $result=[
                $categoryList,
                $updatedUser
            ];
            //TODO: Them bang lich su trao doi

            return $this->_formatBaseResponse(201, $result, 'Success');
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return $this->_formatBaseResponse(400, $errors, 'Failed');
        }
    }

    public function getCardsByUserIdAndCategoryId(Request $request): array
    {

        try {
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
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return $this->_formatBaseResponse(400, $errors, 'Failed');
        }

    }

}
