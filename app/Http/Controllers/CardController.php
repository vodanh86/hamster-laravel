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
//        $userId = 1;
//        $exchangeId = 1;
        try {

            $dataInput = $request->all();

            $validator = $this->cardValidator->validateGetAllWithCategory($dataInput);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $userId = $dataInput['user_id'];
            $exchangeId = $dataInput['exchange_id'];

//            $categoryList = Category::all();
//
//            $cardList = Card::with(['cardProfits' => function ($query) {
//                $query->orderBy('level', 'asc');
//            }])->get();
//
//            $user_id = 41; // Change this to the desired user ID
//            $exchange_id = 51; // Change this to the desired exchange ID
//            $purchasedCards = Card::join('card_profit', 'card.id', '=', 'card_profit.card_id')
//                ->join('user_card', 'card_profit.id', '=', 'user_card.card_profit_id')
//                ->where('user_card.user_id', $user_id)
//                ->where('user_card.exchange_id', $exchange_id)
//                ->orderBy('card.order', 'asc')
//                ->get();
//
//            foreach ($categoryList as $category) {
//                $categoryCards = $cardList->where('category_id', $category->id)->values();
//                foreach ($categoryCards as $card) {
//                    $cardProfits = $card->cardProfits;
//                    // Check if the card is purchased by the user
//                    $isPurchased = $purchasedCards->contains('id', $card->id);
//                    foreach ($cardProfits as $index => $cardProfit) {
//                        $cardProfitArray = $cardProfit->toArray();
//                        if ($index < $cardProfits->count() - 1) {
//                            $nextLevelProfit = $cardProfits[$index + 1];
//                            if ($nextLevelProfit->level == $cardProfit->level + 1) {
//                                $cardProfitArray['next_level'] = $nextLevelProfit->toArray();
//                                unset($cardProfitArray['next_level']['next_level']); // Remove the recursive next_level
//                            } else {
//                                $cardProfitArray['next_level'] = null;
//                            }
//                        } else {
//                            $cardProfitArray['next_level'] = null; // No next level
//                        }
//                        $cardProfits[$index] = $cardProfitArray;
//                    }
//                    $card->is_purchased = $isPurchased;
//                }
//                $category->cardList = $categoryCards;

            //card da mua
            $categoryList = Category::all();

            $cardList = Card::with(['cardProfits' => function ($query) {
                $query->orderBy('level', 'asc');
            }])->get();

//            $user_id = 41; // Change this to the desired user ID
//            $exchange_id = 51; // Change this to the desired exchange ID

            $purchasedCardProfits = CardProfit::join('user_card', 'card_profit.id', '=', 'user_card.card_profit_id')
                ->where('user_card.user_id', $userId)
                ->where('user_card.exchange_id', $exchangeId)
                ->select('card_profit.card_id', 'card_profit.level')
                ->get();

            $maxLevelByCard = $purchasedCardProfits->groupBy('card_id')
                ->map(function ($profits) {
                    return $profits->max('level');
                });

            foreach ($categoryList as $category) {
                $categoryCards = $cardList->where('category_id', $category->id)->values();

                foreach ($categoryCards as $card) {
                    $cardProfits = $card->cardProfits;

                    $maxLevel = $maxLevelByCard->get($card->id, null);

                    foreach ($cardProfits as $index => $cardProfit) {
                        $cardProfitArray = $cardProfit->toArray();

                        if ($cardProfit->level == $maxLevel) {
                            $cardProfitArray['is_purchased'] = true;
                        } else {
                            $cardProfitArray['is_purchased'] = false;
                        }

                        if ($index < $cardProfits->count() - 1) {
                            $nextLevelProfit = $cardProfits[$index + 1];
                            if ($nextLevelProfit->level == $cardProfit->level + 1) {
                                $cardProfitArray['next_level'] = $nextLevelProfit->toArray();
                                unset($cardProfitArray['next_level']['next_level']); // Remove the recursive next_level
                            } else {
                                $cardProfitArray['next_level'] = null;
                            }
                        } else {
                            $cardProfitArray['next_level'] = null; // No next level
                        }
                        $cardProfits[$index] = $cardProfitArray;
                    }
                }

                $category->cardList = $categoryCards;
            }


            return $this->_formatBaseResponse(200, $categoryList, 'Success');
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return $this->_formatBaseResponse(400, $errors, 'Failed');
        }
    }

}
