<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Skin;
use Illuminate\Http\Request;

class SkinController extends Controller
{
    //
    public function index(Request $request)
    {
        return Skin::all();
    }
    
}
