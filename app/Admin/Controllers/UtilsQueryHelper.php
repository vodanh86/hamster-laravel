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
        return ProfitPerHour::all()
            ->where('user_id','=',$userId)
            ->where('is_active','=',ConstantHelper::STATUS_ACTIVE)
            ->first;
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

    public static function findProfitPerHourByUserAndExchange($userId, $exchangeId):?ProfitPerHour
    {
        return ProfitPerHour::all()
            ->where('user_id', '=',$userId)
            ->where('exchange_id', '=',$exchangeId)
            ->where('is_active', '=',ConstantHelper::STATUS_ACTIVE)
            ->first();
    }

    public static function findNextMemebership($currentLevel, $membershipId){
        return Membership::all()
            ->where('level','>',$currentLevel)
            ->sort('level', 'asc')
            ->first();


    }


}
