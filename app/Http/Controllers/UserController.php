<?php

namespace App\Http\Controllers;

use App\Http\Validators\UserValidator;
use App\Traits\ResponseFormattingTrait;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    use ResponseFormattingTrait;

    protected $userValidator;

    public function __construct(UserValidator $userValidator)
    {
        $this->userValidator = $userValidator;
    }

    //Khi bat app, check user ton tai chua, neu chua se tu tao
    public function index(Request $request)
    {
        $tg_data = $request->post("tg_data");
        var_dump($request->post);
        if (!is_null($tg_data)) {
            $tg_data = urldecode($tg_data);
            parse_str($tg_data, $params);
            $tele_user = json_decode($params["user"], true);
            $user = User::where("telegram_id", $tele_user["id"])->first();
            if ($user) {
                $user->last_login = Carbon::now();
                $user->is_first_login = 0;
                $user->update();
            } else {
                $user = new User();
                $user->telegram_id = $tele_user["id"];
                $user->first_name = $tele_user["first_name"];
                $user->last_name = $tele_user["last_name"];
                $user->username = $tele_user["username"];
                $user->language_code = $tele_user["language_code"];
                $user->revenue = 0;
                $user->highest_score = 0;
                $user->skin_id = 0;
                $user->membership_id = 0;
                $user->is_first_login = 1;
                $user->save();
            }
            return json_encode($user);
        }
    }

    public function store(Request $request): array
    {
        try {
            $dataInput = $request->all();

            $validator = $this->userValidator->validateLogin($dataInput);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $telegram_id = $dataInput['telegram_id'];
            $user = User::where("telegram_id", $telegram_id)->first();
            if ($user) {
                $user->last_login = Carbon::now();
                $user->is_first_login = 0;
                $user->update();
            } else {
                $user = new User();
                $user->telegram_id = $telegram_id;
                $user->first_name = $dataInput["first_name"];
                $user->last_name = $dataInput["last_name"];
                $user->username = $dataInput["username"];
                $user->language_code = $dataInput["language_code"];
                $user->revenue = 0;
                $user->highest_score = 0;
                $user->skin_id = 0;
                $user->membership_id = 0;
                $user->is_first_login = 1;
                $user->created_at = now()->toDateTime();
                $user->save();
            }

            return $this->_formatBaseResponse(201, $user, 'Success');
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return $this->_formatBaseResponse(400, $errors, 'Failed');
        }
    }

    public function userInfor($id): ?array
    {
        try {
            $user = User::findOrFail($id);
            return $this->_formatBaseResponse('200', $user, 'Success');
        } catch (ModelNotFoundException $e) {
            return $this->_formatBaseResponse('404', ['error' => 'User not found'], 'Failed');
        }
    }

    public function updateRevenue(Request $request): ?array
    {
        try {
            $dataInput = $request->all();

            $validator = $this->userValidator->validateUpdateRevenue($dataInput);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user_id = $dataInput['user_id'];
            $amount = $dataInput['amount'];
            $user = User::findOrFail($user_id);
            if ($user) {
                //get current value
                $currentRevenue = (int)$user->revenue;
                $user->revenue = $currentRevenue + $amount;

                $user->update();

                $result = [
                    'user_id' => $user_id,
                    'revenue' => $user->revenue
                ];

                return $this->_formatBaseResponse(200, $result, 'Success');
            } else {
                return $this->_formatBaseResponse(400, null, 'Failed');
            }

        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return $this->_formatBaseResponse(400, $errors, 'Failed');
        }
    }


}
