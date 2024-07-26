<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\ResponseFormattingTrait;
use Carbon\Carbon;
use App\Models\Membership;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    use ResponseFormattingTrait;

    public function index(Request $request)
    {
        $data = Membership::all();
        return $this->_formatBaseResponse(200, $data, 'Success');
    }
    
}
