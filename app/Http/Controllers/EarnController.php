<?php

namespace App\Http\Controllers;

use App\Admin\Controllers\ConstantHelper;
use App\Admin\Controllers\UtilsQueryHelper;
use App\Http\Validators\EarnValidator;
use App\Models\Earn;
use App\Models\User;
use App\Models\UserEarn;
use App\Traits\ResponseFormattingTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EarnController extends Controller
{
    use ResponseFormattingTrait;

    protected $earnValidator;

    /**
     * @param EarnValidator $earnValidator
     */
    public function __construct(EarnValidator $earnValidator)
    {
        $this->earnValidator = $earnValidator;
    }


    public function getEarnByUser(Request $request): array
    {
        try {
            $dataInput = $request->all();

            $validator = $this->earnValidator->validateGetByUser($dataInput);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $userId = $dataInput['user_id'];

            $earns = (new UtilsQueryHelper())::getEarnByUser($userId);

            return $this->_formatBaseResponse(200, $earns, 'Success');
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return $this->_formatBaseResponse(400, $errors, 'Failed');
        }
    }

    public function updateEarn(Request $request): array
    {
        try {
            $dataInput = $request->all();

            $validator = $this->earnValidator->validateUpdateEarnByUser($dataInput);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $userId = $dataInput['user_id'];
            $userEarnId = $dataInput['user_earn_id'];
//            $isCompleted = $dataInput['is_completed'];

            //TODO: sau them check nhiem vu nua. Hien tai click vao thi auto completed
            $userEarn = UserEarn::findOrFail($userEarnId);
            $user=new User();
            if ($userEarn) {
                $userEarnStatus = $userEarn->is_completed;
                $earnId = $userEarn->earn_id;

                $earn = Earn::findOrFail($earnId);
                if (!$earn) {
                    return $this->_formatBaseResponse(400, null, 'Earn not found');
                }
                if ($userEarnStatus === 0) {
                    //check neu la Daily task
                    $earnType = $earn->type;
                    if ($earnType === ConstantHelper::USER_EARN_TYPE_DAILY_EARN) {
                        $preEarns = (new UtilsQueryHelper())::findIsNotCompletedByUser($userId, $earnId, ConstantHelper::USER_EARN_TYPE_DAILY_EARN);
                        if (count($preEarns)) {
                            //co daily quen chua check
                            return $this->_formatBaseResponse(400, null, 'Please claim previously task');
                        }
                    }

                    $userEarn->is_completed = 1;
                    $userEarn->update();

                    //earn
                    $reward = $earn->reward;
                    //cong tien
                    $user = User::findOrFail($userId);
                    if ($user) {
                        $currentRevenue = (int)$user->revenue;
                        $currentHighestScore = $user->highest_score;
                        $newRevenue = $currentRevenue + $reward;
                        if ($newRevenue > $currentHighestScore) {
                            $user->highest_score = $newRevenue;
                        }
                        $user->revenue = $newRevenue;

                        $user->update();

                    } else {
                        return $this->_formatBaseResponse(400, null, 'User not found');
                    }
                }
            } else {
                return $this->_formatBaseResponse(400, null, 'User Earn not found');
            }

            $earns = (new UtilsQueryHelper())::getEarnByUser($userId);
            $membership = (new UtilsQueryHelper())::findMemberShipByUser($userId);

            $result=[
                "earns"=>$earns,
                "membership"=>$membership,
                'user'=>$user
            ];

            return $this->_formatBaseResponse(200, $result, 'Success');


        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return $this->_formatBaseResponse(400, $errors, 'Failed');
        }
    }

}
