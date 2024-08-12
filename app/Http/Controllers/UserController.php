<?php

namespace App\Http\Controllers;

use App\Admin\Controllers\ConstantHelper;
use App\Admin\Controllers\UtilsQueryHelper;
use App\Http\Validators\UserValidator;
use App\Models\Membership;
use App\Models\ProfitPerHour;
use App\Models\Skin;
use App\Models\UserBoots;
use App\Models\UserEarn;
use App\Models\UserFriend;
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
            $reference_id = $dataInput['reference_telegram_id'];

            $user = User::where("telegram_id", $telegram_id)->first();
            $userId = null;
            if ($user) {
                $user->last_login = Carbon::now();
                $user->is_first_login = 0;
                $user->update();
                $profitPerHour = (new UtilsQueryHelper())::getActiveExchangeByUser($user->id);
                $user->profitPerHour = $profitPerHour;
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

                //check reference or not
                if (!is_null($reference_id)) {
                    //insert bang user friend
                    $userFriend = new UserFriend();
                    $userFriend->user_id = $user->id;
                    $userFriend->reference_id = $reference_id;
                    $userFriend->type = ConstantHelper::USER_FRIEND_TYPE_FRIEND;

                    $userFriend->save();


                    ////cong diem reference
                    //cong diem nguoi gioi thieu
                    $referenceUser = User::findOrFail($reference_id);
                    if ($referenceUser) {
                        $referenceUser->revenue += 10000;
                        $referenceUser->update();
                    }
                    //cong diem nguoi duoc gioi thieu
                    $user->revenue += 5000;
                    $user->update();

                }

                //save exchangeId  into ProfitPerHour
                $userId = $user->id;
                $exchanges = (new UtilsQueryHelper())::getAllExchanges();
                $flagActive = true;
                for ($i = 0, $iMax = count($exchanges); $i < $iMax; $i++) {
                    $profitPerHour = new ProfitPerHour();
                    $profitPerHour->user_id = $userId;
                    $profitPerHour->exchange_id = $exchanges[$i]->id;
                    $profitPerHour->profit_per_hour = 2000;
                    if ($flagActive) {
                        $profitPerHour->is_active = 1;
                        $flagActive = false;
                    } else {
                        $profitPerHour->is_active = 0;
                    }
                    $profitPerHour->save();
                }
//                $user->profitPerHour = (new UtilsQueryHelper())::getProfitPerHourByUser($userId);

                //add earn
                $earns = (new UtilsQueryHelper())::getAllEarns();

                for ($j = 0, $jMax = count($earns); $j < $jMax; $j++) {
                    $userEarn = new UserEarn();
                    $userEarn->user_id = $userId;
                    $userEarn->earn_id = $earns[$j]->id;
                    $userEarn->is_completed = ConstantHelper::STATUS_IN_ACTIVE;

                    $userEarn->save();
                }

//                $user->earns = (new UtilsQueryHelper())::getEarnByUser($userId);

                //add boots
                $boots = (new UtilsQueryHelper())::getAllBoots();

                for ($k = 0, $kMax = count($boots); $k < $kMax; $k++) {
                    $userBoots = new UserBoots();
                    $userBoots->user_id = $userId;
                    $userBoots->boots_id = $boots[$k]->id;
                    $userBoots->is_completed = ConstantHelper::STATUS_IN_ACTIVE;

                    $userBoots->save();
                }

//                $user->boots = (new UtilsQueryHelper())::getBootsByUser($userId);

            }
            $userId = $user->id;
            $user->profitPerHour = (new UtilsQueryHelper())::getProfitPerHourByUser($userId);
//            $user->earns = (new UtilsQueryHelper())::getEarnByUser($userId);

            $user->boots = (new UtilsQueryHelper())::getBootsByUser($userId);

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
                $currentHighestScore = $user->highest_score;
                $newRevenue = $currentRevenue + $amount;
                //check if current revenue > highest score
                if ($currentRevenue > $currentHighestScore) {
                    $user->highest_score = $currentRevenue;
                    $currentHighestScore = $currentRevenue;
                }
                $user->revenue = $newRevenue;

                $user->update();

                $result = [
                    'user_id' => $user_id,
                    'revenue' => $user->revenue
                ];

                //update membership
                //TODO: check xem next membership
                $membership = Membership::findOrFail($user->membership_id);
                $nextMembership = (new UtilsQueryHelper())::findNextMemebership($membership->level);
                $nextMembershipMoney = $nextMembership->money;
                if ($currentHighestScore >= $nextMembershipMoney) {
                    $user->membership_id = $nextMembership->id;
                    $user->update();
                }

                return $this->_formatBaseResponse(200, $result, 'Success');
            } else {
                return $this->_formatBaseResponse(400, null, 'Failed');
            }

        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return $this->_formatBaseResponse(400, $errors, 'Failed');
        }
    }

    public function updateSkin(Request $request): ?array
    {
        try {
            $dataInput = $request->all();

            $validator = $this->userValidator->validateUpdateSkin($dataInput);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $userId = $dataInput['user_id'];
            $skinId = $dataInput['skin_id'];

            $skin = Skin::findOrFail($skinId);

            if (!$skin) {
                return $this->_formatBaseResponse(400, null, 'Skin not found');
            }
            $skinPrice = $skin->price;

            $user = User::findOrFail($userId);
            if ($user) {
                $currentSkin = $user->skin_id;
                //validate skin
                if ($currentSkin === $skinId) {
                    return $this->_formatBaseResponse(400, null, 'Cannot buy current skin again.');
                }

                //get current revenue
                $currentRevenue = (int)$user->revenue;
                if ($currentRevenue < $skinPrice) {
                    return $this->_formatBaseResponse(400, null, 'Revenue is not enough to buy this skin.');
                }

                //buy the skin
                $user->skin_id = $skinId;
                $user->revenue = $currentRevenue - $skinPrice;
                $user->update();
            }

            $result = [
                'user' => $user,
                'skin' => $skin
            ];

            return $this->_formatBaseResponse(200, $result, 'Success');

        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return $this->_formatBaseResponse(400, $errors, 'Failed');
        }
    }

    public function getRankByMembership(Request $request): array
    {
        try {
            $dataInput = $request->all();

            $validator = $this->userValidator->validateGetRankByMembership($dataInput);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $userId = $dataInput['user_id'];
            $memberships = (new UtilsQueryHelper())::getAllMemberships();

            $currentUser = (new UtilsQueryHelper())::findUserByMembershipAndUser($userId);
            $user = User::findOrFail($userId);
            $userMemberShipId = $user->membership_id;
            foreach ($memberships as $iValue) {
                $membership = $iValue;
                $userByRanks = (new UtilsQueryHelper())::findUserByMembership($membership->id);

                $membership->rank = $userByRanks;
                if ($userMemberShipId === $membership->id) {
                    $membership->currentUser = $currentUser;
                }
            }


            return $this->_formatBaseResponse('200', $memberships, 'Success');
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return $this->_formatBaseResponse(400, $errors, 'Failed');
        }
    }

    public function getFriendsByUser($id): ?array
    {
        try {
            $user = User::findOrFail($id);

            $userFriendList = UserFriend::where('reference_id', $id)->get();
            // Get user details for these friends
            $userFriends = $userFriendList->map(function ($userFriend) {
                return $userFriend->user;
            });

            $result = [
                'user' => $user,
                "userFriends" => $userFriends
            ];

            return $this->_formatBaseResponse('200', $result, 'Success');
        } catch (ModelNotFoundException $e) {
            return $this->_formatBaseResponse('404', ['error' => 'User not found'], 'Failed');
        }
    }

}
