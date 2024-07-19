<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function index(Request $request)
    {
        $data = $request->post("data");
        if (!is_null($data)){
            $tg_data = ($data["tg_data"]);
            if (!is_null($tg_data)){
                $tg_data = urldecode($tg_data);
                parse_str($tg_data, $params);
                $tele_user = json_decode($params["user"], true);
                $user = new User();
                $user->telegram_id = $tele_user["id"];
                $user->first_name = $tele_user["first_name"];
                $user->last_name = $tele_user["last_name"];
                $user->username = $tele_user["username"];
                $user->language_code = $tele_user["language_code"];
                $user->save();
            }
        }
    }
    
}
