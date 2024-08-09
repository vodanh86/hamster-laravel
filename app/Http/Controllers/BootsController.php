<?php

namespace App\Http\Controllers;

use App\Admin\Controllers\ConstantHelper;
use App\Admin\Controllers\UtilsQueryHelper;
use App\Http\Validators\BootsValidator;
use App\Http\Validators\EarnValidator;
use App\Models\Boots;
use App\Models\Earn;
use App\Models\Membership;
use App\Models\User;
use App\Models\UserBoots;
use App\Models\UserEarn;
use App\Traits\ResponseFormattingTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BootsController extends Controller
{
    use ResponseFormattingTrait;

    protected $bootsValidator;

    /**
     * @param BootsValidator $earnValidator
     */
    public function __construct(BootsValidator $bootsValidator)
    {
        $this->bootsValidator = $bootsValidator;
    }


    public function getBootsByUser(Request $request): array
    {
        try {
            $dataInput = $request->all();

            $validator = $this->bootsValidator->validateGetByUser($dataInput);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $userId = $dataInput['user_id'];

            $boots = (new UtilsQueryHelper())::getBootsByUser($userId);

            return $this->_formatBaseResponse(200, $boots, 'Success');
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return $this->_formatBaseResponse(400, $errors, 'Failed');
        }
    }

    public function updateBoots(Request $request): array
    {
        try {
            $dataInput = $request->all();

            $validator = $this->bootsValidator->validateUpdateBootsByUser($dataInput);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $userId = $dataInput['user_id'];
            $currentUserBootsId = $dataInput['current_user_boots_id'];
            $nextUserBootsId = $dataInput['next_user_boots_id'];
            $currentLevel = $dataInput['current_boots_level'];
            $nextLevel = $dataInput['next_boots_level'];
            $type = $dataInput['type'];
            $subType = $dataInput['sub_type'];

            $currentUserBoots = UserBoots::findOrFail($currentUserBootsId);
            $nextUserBoots = UserBoots::findOrFail($nextUserBootsId);
//            error_log('current and next USER BOOTS');
//            error_log(json_encode($currentUserBoots));
//            error_log(json_encode($nextUserBoots));
            $currentBoots=Boots::findOrFail($currentUserBoots->boots_id);
            $nextBoots=Boots::findOrFail($nextUserBoots->boots_id);
//            error_log('current and next  ');
//            error_log(json_encode($currentBoots));
//            error_log(json_encode($nextBoots));

            //FREE OR FEE

            $user = User::findOrFail($userId);
//            $currentMultiTap = $user->tap_value;


            if ($type === ConstantHelper::BOOTS_TYPE_FREE) {
                $currentUserBoots->is_completed = false;
                $nextUserBoots->is_completed = true;
            } elseif ($type === ConstantHelper::BOOTS_TYPE_FEE) {

                $requiredMoney = $nextBoots->required_money;
//                error_log('requiredMoney: ' . $requiredMoney);
                //tru revenue
                $user->revenue -= $requiredMoney;
//                error_log('new revenue:'. $user->revenue);
                $increaseValue = ($nextBoots->value) - ($currentBoots->value);
                if ($subType === ConstantHelper::BOOTS_SUBTYPE_MULTI_TAP) {
                    $user->tap_value += $increaseValue;
//                    error_log('tap new: ' . $user->tap_value);
                }
                if ($subType === ConstantHelper::BOOTS_SUBTYPE_ENERGY_LIMIT) {
                    $user->energy_limit += $increaseValue;
                }

                $currentUserBoots->is_completed = false;
                $nextUserBoots->is_completed = true;

            } else {
                return $this->_formatBaseResponse(400, null, 'Invalid type');
            }

            $user->update();
            $currentUserBoots->update();
            $nextUserBoots->update();

            $earns = (new UtilsQueryHelper())::getEarnByUser($userId);

            $membership = (new UtilsQueryHelper())::findMemberShipByUser($userId);

            $boots = (new UtilsQueryHelper())::getBootsByUser($userId);

            $maxEnergy = $user->energy_limit;;

            $result = [
                "earns" => $earns,
                "membership" => $membership,
                'user' => $user,
                'boots' => $boots,
                'max_energy' => $maxEnergy
            ];

            return $this->_formatBaseResponse(200, $result, 'Success');


        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return $this->_formatBaseResponse(400, $errors, 'Failed');
        }
    }

}
