<?php

namespace App\Http\Controllers;

use App\Models\CRM\Cost;
use App\Models\CRM\CostClosure;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }
}
