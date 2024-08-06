<?php

namespace App\Http\Controllers;

use App\Admin\Controllers\UtilsQueryHelper;
use App\Http\Validators\EarnValidator;
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

}
