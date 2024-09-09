<?php

namespace App\Http\Controllers;

use App\Models\HomeScreen;
use App\Traits\ResponseFormattingTrait;
use Carbon\Carbon;
use App\Models\Skin;
use Illuminate\Http\Request;

class HomeScreenController extends Controller
{
    use ResponseFormattingTrait;

    public function index(Request $request)
    {
        $data = HomeScreen::all();
        return $this->_formatBaseResponse(200, $data, 'Success');
    }

}
