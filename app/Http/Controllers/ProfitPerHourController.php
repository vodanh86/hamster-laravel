<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ProfitPerHour;
use Illuminate\Http\Request;

class ProfitPerHourController extends Controller
{
    //
    public function index(Request $request)
    {
        return ProfitPerHour::all();
    }
    
}
