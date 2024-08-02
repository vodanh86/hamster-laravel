<?php

namespace App\Http\Controllers;

use App\Traits\ResponseFormattingTrait;
use Carbon\Carbon;
use App\Models\Skin;
use Illuminate\Http\Request;

class SkinController extends Controller
{
    use ResponseFormattingTrait;

    public function index()
    {
        $data = Skin::all();
        return $this->_formatBaseResponse(200, $data, 'Success');
    }

}
