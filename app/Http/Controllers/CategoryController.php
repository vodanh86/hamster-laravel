<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\ResponseFormattingTrait;
use Carbon\Carbon;
use App\Models\Skin;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ResponseFormattingTrait;

    public function index(Request $request)
    {
        $data = Category::all();
        return $this->_formatBaseResponse(200, $data, 'Success');
    }

}
