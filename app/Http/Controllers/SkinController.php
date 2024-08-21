<?php

namespace App\Http\Controllers;

use App\Admin\Controllers\UtilsQueryHelper;
use App\Http\Validators\SkinValidator;
use App\Models\User;
use App\Traits\ResponseFormattingTrait;
use Carbon\Carbon;
use App\Models\Skin;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SkinController extends Controller
{
    use ResponseFormattingTrait;

    protected $skinValidator;

    /**
     * @param SkinValidator $userCardValidator
     */
    public function __construct(SkinValidator $skinValidator)
    {
        $this->skinValidator = $skinValidator;
    }

    public function index()
    {
        $data = Skin::all();
        return $this->_formatBaseResponse(200, $data, 'Success');
    }

    public function buySkin(Request $request): array
    {
        try {
            $dataInput = $request->all();

            $validator = $this->skinValidator->validateUserBuySkin($dataInput);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
            $skinId = $dataInput["skin_id"];
            $userId = $dataInput["user_id"];

            $user = User::findOrFail($userId);

            $currentRevenue = (int)$user->revenue;

            $currentSkin = Skin::findOrFail($user->skin_id);
            $newSkin=null;

            //TODO: Check if skinId=-1
            if ($skinId === -1) {
                //ko dung skin
                $user->skin_id = -1;
            } else {
                //next level
                $nextSkin = Skin::findOrFail($skinId);
                if (!$nextSkin) {
                    return $this->_formatBaseResponse(400, null, 'Skin is not exist!');
                }
                //kiem tra xem user co du tien mua skin ko
                $requiredPrice = $nextSkin->price;
                if ($currentRevenue < $requiredPrice) {
                    return $this->_formatBaseResponse(400, null, 'Not enough money to buy skin.');
                }

                //TODO: Sau them bang User-Skin

                //update revenue
                $newRevenue = $currentRevenue - $requiredPrice;
                $user->revenue = $newRevenue;
                $user->skin_id = $skinId;

                $newSkin=$nextSkin;
            }

            $user->update();


            $result = [
                'user' => $user,
                'skin' => $newSkin
            ];

            return $this->_formatBaseResponse(201, $result, 'Success');
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return $this->_formatBaseResponse(400, $errors, 'Failed');
        }
    }

}
