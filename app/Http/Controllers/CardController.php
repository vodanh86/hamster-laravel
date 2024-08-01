<?php

namespace App\Http\Controllers;

use App\Admin\Controllers\UtilsQueryHelper;
use App\Http\Validators\CardValidator;
use App\Models\Card;
use App\Models\CardProfit;
use App\Models\Category;
use App\Models\Membership;
use App\Models\User;
use App\Models\UserCard;
use App\Traits\ResponseFormattingTrait;
use Carbon\Carbon;
use App\Models\Skin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CardController extends Controller
{
    use ResponseFormattingTrait;

    protected $cardValidator;

    /**
     * @param CardValidator $cardValidator
     */
    public function __construct(CardValidator $cardValidator)
    {
        $this->cardValidator = $cardValidator;
    }


    public function getByCategory(Request $request): array
    {
        try {
            $dataInput = $request->all();

            $validator = $this->cardValidator->validateGetByCategory($dataInput);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $categoryId = $dataInput['category_id'];


            //lay card profit level 0 theo card o
            $cards = Card::where('category_id', $categoryId)
                ->with(['cardProfits' => function ($query) {
                    $query->orderBy('level', 'asc');
                }])
                ->get();

            $cards->each(function ($card) {
                if ($card->cardProfits->isNotEmpty()) {
                    $card->card_profits = $card->cardProfits->sortBy('level')->take(1)->map(function ($cardProfit) {
                        $requiredCardId = $cardProfit->required_card;
                        $combinedCard = (new UtilsQueryHelper())::getCombinedCardbyId($requiredCardId);
                        $cardProfit->required_card_text = $combinedCard;
                        return $cardProfit;
                    });
                } else {
                    $card->card_profits = collect();
                }
                unset($card->cardProfits);
            });

            return $this->_formatBaseResponse(200, $cards, 'Success');
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return $this->_formatBaseResponse(400, $errors, 'Failed');
        }
    }

    public function getAllWithCategory(Request $request): array
    {
        try {
            $dataInput = $request->all();

            $validator = $this->cardValidator->validateGetAllWithCategory($dataInput);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $userId = $dataInput['user_id'];
            $exchangeId = $dataInput['exchange_id'];

            $categoryList = (new UtilsQueryHelper())::listCardByUserAndExchange($userId, $exchangeId);

            return $this->_formatBaseResponse(200, $categoryList, 'Success');
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return $this->_formatBaseResponse(400, $errors, 'Failed');
        }
    }

}
