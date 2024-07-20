<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function index(Request $request)
    {
        $tg_data = $request->post("tg_data");
        var_dump($request->post);
        if (!is_null($tg_data)){
            $tg_data = urldecode($tg_data);
            parse_str($tg_data, $params);
            $tele_user = json_decode($params["user"], true);
            $user = User::where("telegram_id", $tele_user["id"])->first();
            if ($user) {
                $user->last_login = Carbon::now();
                $user->update();
            } else {
                $user = new User();
                $user->telegram_id = $tele_user["id"];
                $user->first_name = $tele_user["first_name"];
                $user->last_name = $tele_user["last_name"];
                $user->username = $tele_user["username"];
                $user->language_code = $tele_user["language_code"];
                $user->save();
            }
            return json_encode($user);
        }
    }
    
}
