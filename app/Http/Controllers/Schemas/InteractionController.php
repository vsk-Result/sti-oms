<?php

namespace App\Http\Controllers\Schemas;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class InteractionController extends Controller
{
    public function index(): View
    {
        return view('schemas.interactions');
    }
}
