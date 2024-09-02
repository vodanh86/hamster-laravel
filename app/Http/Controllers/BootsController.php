<?php

namespace App\Http\Controllers;

use App\Admin\Controllers\ConstantHelper;
use App\Admin\Controllers\UtilsQueryHelper;
use App\Http\Validators\BootsValidator;
use App\Models\Boots;
use App\Models\User;
use App\Models\UserBoots;
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
            $nextBoots=Boots::findOrFail($nextUserBoots->boots_id);

            //FREE OR FEE

            $user = User::findOrFail($userId);

            if ($type === ConstantHelper::BOOTS_TYPE_FREE) {
                $currentUserBoots->is_completed = false;
                $nextUserBoots->is_completed = true;
            } elseif ($type === ConstantHelper::BOOTS_TYPE_FEE) {

                $requiredMoney = $nextBoots->required_money;
                //tru revenue
                $user->revenue -= $requiredMoney;
                $increaseValue = $nextBoots->value;
                if ($subType === ConstantHelper::BOOTS_SUBTYPE_MULTI_TAP) {
                    $user->tap_value = $increaseValue;
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

//            $earns = (new UtilsQueryHelper())::getEarnByUser($userId);

            $membership = (new UtilsQueryHelper())::getMemberShipByUserV02($userId);

            $boots = (new UtilsQueryHelper())::getBootsByUser($userId);

            $maxEnergy = $user->energy_limit;

            $profitPerHour=(new UtilsQueryHelper())::getProfitPerHourByUser($userId);

            $result = [
//                "earns" => $earns,
                "membership" => $membership,
                'user' => $user,
                'boots' => $boots,
                'max_energy' => $maxEnergy,
                "profitPerHour" => $profitPerHour
            ];

            return $this->_formatBaseResponse(200, $result, 'Success');


        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return $this->_formatBaseResponse(400, $errors, 'Failed');
        }
    }

}
