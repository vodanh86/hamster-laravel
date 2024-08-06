<?php

namespace App\Admin\Controllers;

use App\Models\Card;
use App\Models\CardProfit;
use App\Models\Category;
use App\Models\Exchange;
use App\Models\Membership;
use App\Models\ProfitPerHour;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UtilsQueryHelper
{
    public static function findUserById($userId)
    {
        return User::all()
            ->where('id', '=', $userId)
            ->first();
    }

    public static function getAllCategories(): Collection
    {
        return Category::all()->pluck('name', 'id');
    }

    public static function getAllCards(): Collection
    {
        return Card::all()->pluck('name', 'id');
    }

    public static function getCombinedCard(): Collection
    {
        $data = CardProfit::with('card')->get()
            ->map(function ($cardProfit) {
                error_log($cardProfit);
                $display_name = $cardProfit->card->name . ' - Level ' . $cardProfit->level;

                return [
                    'id' => $cardProfit->id,
                    'display_name' => $display_name
                ];
            })
            ->pluck('display_name', 'id');
        return $data;
    }

    public static function getCombinedCardById(int $cardProfitId): string
    {
        $cardProfit = CardProfit::with('card')->find($cardProfitId);

        if ($cardProfit && $cardProfit->card) {
            return $cardProfit->card->name . ' - Level ' . $cardProfit->level;
        }

        return 'Không có';
    }

    public static function getFirstExchange(): ?Exchange
    {
        return Exchange::all()
            ->first();
    }

    public static function getAllExchanges()
    {
        return Exchange::all();
    }

    public static function getProfitPerHourByUser($userId)
    {
        $data= ProfitPerHour::all()
            ->where('user_id', '=', $userId)
            ->where('is_active', '=', ConstantHelper::STATUS_ACTIVE)
            ->first();
        return $data;
    }

    public static function getActiveExchangeByUser($userId): ?ProfitPerHour
    {
        return ProfitPerHour::all()->where('user_id', $userId)
            ->where('is_active', '=', 1)
            ->first();
    }

    public static function findCardProfitById($id)
    {
        return CardProfit::all()
            ->where('id', $id)
            ->first();
    }

    public static function findPrevCardProfitByCardAndCardProfit($card, $cardProfit)
    {
        return DB::table('card_profit as cp1')
            ->select('cp1.id', 'cp1.card_id', 'cp1.level', 'cp1.profit', 'cp1.required_card', 'cp1.required_money', 'cp1.required_short_money')
            ->where('cp1.card_id', $card)
            ->where('cp1.level', '<', function ($query) use ($cardProfit) {
                $query->select('cp.level')
                    ->from('card_profit as cp')
                    ->where('cp.id', $cardProfit);
            })->orderByDesc('cp1.level')->first();
    }

    public static function findCardProfitByCardAndLevel($card, $level)
    {
        return DB::table('card_profit as cp1')
            ->select('cp1.id', 'cp1.card_id', 'cp1.level', 'cp1.profit', 'cp1.required_card', 'cp1.required_money', 'cp1.required_short_money')
            ->where('cp1.card_id', $card)
            ->where('cp1.level', $level)->first();
    }

    public static function findProfitPerHourByUserAndExchange($userId, $exchangeId): ?ProfitPerHour
    {
        return ProfitPerHour::all()
            ->where('user_id', '=', $userId)
            ->where('exchange_id', '=', $exchangeId)
            ->where('is_active', '=', ConstantHelper::STATUS_ACTIVE)
            ->first();
    }

    public static function findNextMemebership($currentLevel)
    {
        return Membership::where('level', '>', $currentLevel)
            ->orderBy('level', 'asc')
            ->first();
    }

    public static function listCardByUserAndExchange($userId, $exchangeId)
    {
        //card da mua
        $categoryList = Category::all();

        $cardList = Card::with(['cardProfits' => function ($query) {
            $query->orderBy('level', 'asc');
        }])->get();

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

        return $categoryList;
    }

    public static function findUserByMembership($membershipId)
    {
        return DB::table('users as us')
            ->join('profit_per_hours as pph', 'us.id', '=', 'pph.user_id')
            ->join('exchanges as ex', 'ex.id', '=', 'pph.exchange_id')
            ->where('pph.is_active', '=', ConstantHelper::STATUS_ACTIVE)
            ->where('us.membership_id', '=', $membershipId)
            ->select('us.id', 'us.first_name', 'us.last_name', 'us.highest_score', 'pph.exchange_id', 'ex.name', 'ex.image')
            ->orderByDesc('highest_score', 'asc')
            ->limit(100)
            ->get();
    }

    public static function findUserByMembershipAndUser( $userId)
    {
        return DB::table('users as us')
            ->join('profit_per_hours as pph', 'us.id', '=', 'pph.user_id')
            ->join('exchanges as ex', 'ex.id', '=', 'pph.exchange_id')
            ->where('pph.is_active', '=', ConstantHelper::STATUS_ACTIVE)
            ->where('us.id', '=', $userId)
            ->select('us.id', 'us.first_name', 'us.last_name', 'us.highest_score', 'pph.exchange_id', 'ex.name', 'ex.image')
            ->orderByDesc('highest_score', 'asc')
            ->limit(1)
            ->get();
    }

    public static function getAllMemberships()
    {
        return Membership::all()->sortBy('level')->values();
    }

}
