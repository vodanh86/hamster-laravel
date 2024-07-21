<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Exchange;
use Illuminate\Http\Request;

class ExchangeController extends Controller
{
    //
    public function index(Request $request)
    {
        return Exchange::all();
    }
    
}
