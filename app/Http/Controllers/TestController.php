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
        //user-skin
        $users = (new UtilsQueryHelper())::getAllUsers();
        foreach ($users as $user) {
            $skins=(new UtilsQueryHelper())::getAllSkins();
            foreach ($skins as $skin) {
                $userEarn = new UserSkin();
                $userEarn->user_id = $user->id;
                $userEarn->skin_id = $skin->id;
                $userEarn->is_purchased = ConstantHelper::STATUS_IN_ACTIVE;
                $userEarn->save();
            }

        }
        return (new UtilsQueryHelper())::findMemberShipByUser(1);
    }

}
