<?php

namespace App\Http\Controllers;

use App\Admin\Controllers\UtilsQueryHelper;
use App\Http\Validators\CardValidator;
use App\Models\Card;
use App\Models\CardProfit;
use App\Models\Category;
use App\Models\Membership;
use App\Models\User;
use App\Models\UserCard;
use App\Traits\ResponseFormattingTrait;
use Carbon\Carbon;
use App\Models\Skin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TestController extends Controller
{
    use ResponseFormattingTrait;

    public function testMethod()
    {
       return (new UtilsQueryHelper())::findMemberShipByUser(1);
    }

}
