<?php

namespace App\Http\Controllers;

use App\Admin\Controllers\UtilsQueryHelper;
use App\Http\Validators\MembershipValidator;
use App\Models\Category;
use App\Models\ProfitPerHour;
use App\Models\User;
use App\Traits\ResponseFormattingTrait;
use Carbon\Carbon;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MembershipController extends Controller
{
    use ResponseFormattingTrait;

    protected $memberShipValidator;

    /**
     * @param $memberShipValidator
     */
    public function __construct(MembershipValidator $memberShipValidator)
    {
        $this->memberShipValidator = $memberShipValidator;
    }

    public function index(Request $request)
    {
        $data = Membership::all();
        return $this->_formatBaseResponse(200, $data, 'Success');
    }

    public function getByUser(Request $request): array
    {
        try {
            $dataInput = $request->all();

            $validator = $this->memberShipValidator->validateGetByUser($dataInput);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $userId = $dataInput['user_id'];
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
                    "skin" =>(new UtilsQueryHelper())::getSkinByUser($userId),
                    "membership" => $membership,
                    "current_level" => $membership->level,
                    "max_level" => $maxLevel,
                    "required_money" => $requiredMoney,
                    "required_short_money" => $requiredShortMoney
                ];

                return $this->_formatBaseResponse(200, $result, 'Success');
            } else {
                return $this->_formatBaseResponse(200, null, 'Failed');
            }

        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return $this->_formatBaseResponse(400, $errors, 'Failed');
        }
    }

}
