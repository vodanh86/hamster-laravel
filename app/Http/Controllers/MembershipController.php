<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Membership;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    //
    public function index(Request $request)
    {
        return Membership::all();
    }
    
}
