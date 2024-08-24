<?php

namespace App\Admin\Controllers;

use App\Models\Boots;
use App\Models\Card;
use App\Models\CardProfit;
use App\Models\Category;
use App\Models\Earn;
use App\Models\Exchange;
use App\Models\Membership;
use App\Models\ProfitPerHour;
use App\Models\Skin;
use App\Models\User;
use App\Models\UserEarn;
use App\Models\UserSkin;
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

    public static function getAllUsers()
    {
        return User::all();
    }

    public static function getAllSkins()
    {
        return Skin::all();
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
        return CardProfit::with('card')->get()
            ->map(function ($cardProfit) {
                $display_name = $cardProfit->card->name . ' - Level ' . $cardProfit->level;

                return [
                    'id' => $cardProfit->id,
                    'display_name' => $display_name
                ];
            })
            ->pluck('display_name', 'id');
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

    public static function getAllEarns()
    {
        return Earn::all();
    }

    public static function getAllBoots()
    {
        return Boots::all();
    }

    public static function getEarnByUser($userId)
    {
        $data = DB::table('user_earn as ue')
            ->join('earn as ea', 'ea.id', '=', 'ue.earn_id')
            ->where('ue.user_id', '=', $userId)
            ->select('ue.id', 'ue.is_completed', 'ea.id as earn_id', 'ea.name', 'ea.type', 'ea.description', 'ea.link', 'ea.image', 'ea.reward', 'ea.order')
            ->orderBy('ea.type', 'asc')
            ->orderBy('ea.order', 'asc')
            ->get();

        return $data->groupBy('type')->map(function ($items, $type) {
            return [
                'type' => $type,
                'earn' => $items->map(function ($item) {
                    return [
                        'user_earn_id' => $item->id,
                        'is_completed' => $item->is_completed,
                        'name' => $item->name,
                        'description' => $item->description,
                        'link' => $item->link,
                        'image' => $item->image,
                        'reward' => $item->reward,
                        'order' => $item->order
                    ];
                })->toArray()
            ];
        })->values()->toArray();
    }

    public static function getBootsByUser($userId)
    {
        $data = DB::table('user_boots as ue')
            ->join('boots as ea', 'ea.id', '=', 'ue.boots_id')
            ->where('ue.user_id', '=', $userId)
            ->select('ue.id', 'ue.is_completed', 'ea.id as boots_id', 'ea.name', 'ea.required_money', 'ea.required_short_money', 'ea.type', 'ea.sub_type', 'ea.level', 'ea.image', 'ea.value', 'ea.order')
            ->orderBy('ea.type', 'asc')
            ->orderBy('ea.sub_type', 'asc')
            ->orderBy('ea.order', 'asc')
            ->get();

        return $data->groupBy('type')->map(function ($items, $type) {
            return [
                'type' => $type,
                'sub_types' => $items->groupBy('sub_type')->map(function ($subItems, $subType) {
                    return [
                        'sub_type' => $subType,
                        'boots' => $subItems->map(function ($item) {
                            return [
                                'user_boots_id' => $item->id,
                                'boots_id' => $item->boots_id,
                                'name' => $item->name,
                                'required_money' => $item->required_money,
                                'required_short_money' => $item->required_short_money,
                                'is_completed' => $item->is_completed,
                                'level' => $item->level,
                                'image' => $item->image,
                                'value' => $item->value,
                                'order' => $item->order
                            ];
                        })->toArray()
                    ];
                })->values()->toArray()
            ];
        })->values()->toArray();
    }


    public static function getProfitPerHourByUser($userId)
    {
        $data = ProfitPerHour::all()
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

    public static function findMemebershipByMoney($money)
    {
        return Membership::where('money', '<=', $money)
            ->orderBy('level', 'desc')
            ->first();
    }

    public static function listCardByUserAndExchange($userId, $exchangeId)
    {
        //card da mua
        $categoryList = Category::all();

        $cardList = Card::with(['cardProfits' => function ($query) {
            $query->orderBy('level', 'asc');
        }])->orderBy('order', 'asc')
            ->get();

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

                    if ($cardProfit->required_card) {
                        $requiredCardProfit = CardProfit::with('card')->find($cardProfit->required_card);
                        if ($requiredCardProfit) {
                            $requiredCardProfitArray = $requiredCardProfit->toArray();
                            $requiredCardProfitArray['card_name'] = $requiredCardProfit->card->name;
                            unset($requiredCardProfitArray['card']);
                            $cardProfitArray['required_card'] = $requiredCardProfitArray;
                        }
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

    public static function findUserByMembershipAndUser($userId)
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

    public static function findIsNotCompletedByUser($userId, $earnId, $type): Collection
    {
        $subQuery = Earn::where('id', '=', $earnId)
            ->where('type', '=', $type)->first();

        return DB::table('earn as ea')
            ->join('user_earn as ue', 'ea.id', '=', 'ue.earn_id')
            ->where('ea.type', '=', $type)
            ->where('ea.order', '<', $subQuery->order)
            ->where('ue.is_completed', '=', ConstantHelper::STATUS_IN_ACTIVE)
            ->where('ue.user_id', '=', $userId)
            ->select(
                'ea.id'
            )
            ->get();

    }

    public static function findMemberShipByUser($userId): Collection
    {
        return DB::table('memberships as me')
            ->join('users as us', 'us.membership_id', '=', 'me.id')
            ->where('us.id', '=', $userId)
            ->select('us.membership_id', 'me.name', 'me.image', 'me.money', 'me.level', 'me.short_money')
            ->get();
    }


    public static function getMemberShipByUserV02($userId)
    {
        $user = User::with('membership')->find($userId);

        if ($user && $user->membership) {
            $membership = $user->membership;

            //lay so tien de len level
            $requiredMoney = $membership->money;
            $requiredShortMoney = $membership->short_money;

            //lay thu hang theo level
            $maxMembership = Membership::all()->sortByDesc('level')->first();
            $maxLevel = $maxMembership->level;

            $result = [
                "membership" => $membership,
                "current_level" => $membership->level,
                "max_level" => $maxLevel,
                "required_money" => $requiredMoney,
                "required_short_money" => $requiredShortMoney
            ];

            return $result;
        }
        return null;
    }


    public static function getSkinByUser($userId)
    {
        $user=User::findOrFail($userId);
        $skin=$user->skin_id;
        if($skin === -1){
            return null;
        }else{
            return Skin::findOrFail($skin);
        }
    }

    public static function getSkinsBoughtByUser($userId)
    {
        return UserSkin::where('user_id','=',$userId)->get();
    }
}
