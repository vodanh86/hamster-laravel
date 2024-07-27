<?php

namespace App\Http\Controllers;

use App\Http\Validators\CardValidator;
use App\Models\Card;
use App\Models\Category;
use App\Models\Membership;
use App\Models\User;
use App\Traits\ResponseFormattingTrait;
use Carbon\Carbon;
use App\Models\Skin;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CardController extends Controller
{
    use ResponseFormattingTrait;

    protected $cardValidator;

    /**
     * @param CardValidator $cardValidator
     */
    public function __construct(CardValidator $cardValidator)
    {
        $this->cardValidator = $cardValidator;
    }


    public function getByCategory(Request $request): array
    {
        try {
            $dataInput = $request->all();

            $validator = $this->cardValidator->validateGetByCategory($dataInput);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $categoryId = $dataInput['category_id'];


            //lay card profit level 0 theo card o
            $cards = Card::where('category_id', $categoryId)
                ->with(['cardProfits' => function ($query) {
                    $query->orderBy('level', 'asc');
                }])
                ->get();

            $cards->each(function ($card) {
                if ($card->cardProfits->isNotEmpty()) {
                    $card->card_profits = [$card->cardProfits->sortBy('level')->first()];
                } else {
                    $card->card_profits = [];
                }
                unset($card->cardProfits); // Bỏ quan hệ gốc để chỉ giữ lại phần tử đã lọc
            });

            return $this->_formatBaseResponse(200, $cards, 'Success');
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return $this->_formatBaseResponse(400, $errors, 'Failed');
        }
    }

}
