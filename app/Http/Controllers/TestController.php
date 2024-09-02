<?php

namespace App\Http\Controllers;

use App\Admin\Controllers\ConstantHelper;
use App\Admin\Controllers\UtilsQueryHelper;
use App\Http\Validators\CardValidator;
use App\Models\Card;
use App\Models\CardProfit;
use App\Models\Category;
use App\Models\Membership;
use App\Models\User;
use App\Models\UserBoots;
use App\Models\UserCard;
use App\Models\UserSkin;
use App\Traits\ResponseFormattingTrait;
use Carbon\Carbon;
use App\Models\Skin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TestController extends Controller
{
    use ResponseFormattingTrait;

    public function testMethod()
    {
//        //user-skin
////        $users = (new UtilsQueryHelper())::getAllUsers();
////        foreach ($users as $user) {
////            $boots=(new UtilsQueryHelper())::getAllBoots();
////            foreach ($boots as $boot) {
//////                error_log(json_encode($user));
////                $userBoot=UserCard::where('user_id','=',$user->id) ->where('boots_id','=',$boot->id)->first();
//////                error_log(json_encode($userBoot));
////                if(is_null($userBoot)){
////                    $userBoot = new UserBoots();
////                    $userBoot->user_id = $user->id;
////                    $userBoot->boots_id = $boot->id;
////                    $userBoot->is_completed = 0;
////                    $userBoot->save();
////                }
////
////
////            }
//
//
////        }
////        return (new UtilsQueryHelper())::listCardByUserAndExchange(25,51);
//
//        $userId = 23;
//        $exchangeId = 51;
//        //card da mua
//        $categoryList = Category::all();
//
//        $cardList = Card::with(['cardProfits' => function ($query) {
//            $query->orderBy('level', 'asc');
//        }])->orderBy('order', 'asc')
//            ->get();
//
//        $purchasedCardProfits = CardProfit::join('user_card', 'card_profit.id', '=', 'user_card.card_profit_id')
//            ->where('user_card.user_id', $userId)
//            ->where('user_card.exchange_id', $exchangeId)
//            ->select('card_profit.card_id', 'card_profit.level')
//            ->get();
//
//        $maxLevelByCard = $purchasedCardProfits->groupBy('card_id')
//            ->map(function ($profits) {
//                return $profits->max('level');
//            });
//
//        foreach ($categoryList as $category) {
//            $categoryCards = $cardList->where('category_id', $category->id)->values();
//
//            foreach ($categoryCards as $card) {
//                $cardProfits = $card->cardProfits;
//
//                foreach ($cardProfits as $index => $cardProfit) {
////                    error_log(json_encode($cardProfit));
//                    $cardProfitArray = $cardProfit->toArray();
//
//                    //check required_card here
////                    if ($cardProfit->required_card) {
////                        error_log('required_card:' . json_encode($cardProfit->required_card));
////                        $requiredCardProfit = CardProfit::with('card')->find($cardProfit->required_card);
//////                        error_log(json_encode($requiredCardProfit));
////                        //check in user-card
////                        $userCardBought = UserCard::where('user_id', '=', $userId)
////                            ->where('exchange_id', '=', $exchangeId)
////                            ->where('card_profit_id', '=', $requiredCardProfit->required_card)
////                            ->get();
////                        error_log(json_encode($userCardBought));
////                        $requiredCardProfitArray = $requiredCardProfit->toArray();
////                        $requiredCardProfitArray['card_name'] = $requiredCardProfit->card->name;
////                        if (!$userCardBought) {
////                            $requiredCardProfitArray['is_bought'] = 0;
////                        }else{
////                            $requiredCardProfitArray['is_bought'] = 1;
////                        }
////                        unset($requiredCardProfitArray['card']);
////                        $cardProfitArray['required_card'] = $requiredCardProfitArray;
////                    }
//
//                    if ($cardProfit->required_card) {
//                        error_log('required_card:' . json_encode($cardProfit->required_card));
//
//                        // Find the required CardProfit
//                        $requiredCardProfit = CardProfit::with('card')->find($cardProfit->required_card);
//
//                        // Check if the user has bought the required card profit
//                        $userCardBought = UserCard::where('user_id', '=', $userId)
//                            ->where('exchange_id', '=', $exchangeId)
//                            ->where('card_profit_id', '=', $requiredCardProfit->id) // Correctly use the ID
//                            ->exists(); // Use exists() to check if a record is found
//
//                        error_log('User card bought: ' . json_encode($userCardBought));
//
//                        $requiredCardProfitArray = $requiredCardProfit->toArray();
//                        $requiredCardProfitArray['card_name'] = $requiredCardProfit->card->name;
//                        $requiredCardProfitArray['is_bought'] = $userCardBought ? 1 : 0; // Set based on the existence check
//                        unset($requiredCardProfitArray['card']);
//
//                        $cardProfitArray['required_card'] = $requiredCardProfitArray;
//                    }
//
//                    $cardProfits[$index] = $cardProfitArray;
//                }
//            }
//
//            $category->cardList = $categoryCards;
//        }
//
//        return $categoryList;

        $card=Card::findOrFail(1);
        $card->thumbnail('small','photo_column');

        return $card;

    }

}
