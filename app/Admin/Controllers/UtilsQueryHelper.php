<?php

namespace App\Admin\Controllers;

use App\Models\Card;
use App\Models\CardProfit;
use App\Models\Category;
use App\Models\Exchange;
use App\Models\ProfitPerHour;
use App\Models\User;
use Illuminate\Support\Collection;

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

    public static function getActiveExchangeByUser($userId): ?ProfitPerHour
    {
        return ProfitPerHour::all()->where('user_id', $userId)
            ->where('is_active', '=', 1)
            ->first();
    }

    public static function findCardProfitById($id): ?CardProfit
    {
        return CardProfit::all()
            ->where('id', $id)
            ->first();
    }


}
