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
        //user-skin
//        $users = (new UtilsQueryHelper())::getAllUsers();
//        foreach ($users as $user) {
//            $boots=(new UtilsQueryHelper())::getAllBoots();
//            foreach ($boots as $boot) {
////                error_log(json_encode($user));
//                $userBoot=UserCard::where('user_id','=',$user->id) ->where('boots_id','=',$boot->id)->first();
////                error_log(json_encode($userBoot));
//                if(is_null($userBoot)){
//                    $userBoot = new UserBoots();
//                    $userBoot->user_id = $user->id;
//                    $userBoot->boots_id = $boot->id;
//                    $userBoot->is_completed = 0;
//                    $userBoot->save();
//                }
//
//
//            }


//        }
        return (new UtilsQueryHelper())::listCardByUserAndExchange(31,51);
    }

}
