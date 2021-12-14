<?php

namespace App\Http\Controllers\Contract;

use App\Http\Controllers\Controller;
use App\Models\Contract\Act;
use Illuminate\View\View;

class ActController extends Controller
{
    public function index(): View
    {
        $acts = Act::all();
        return view('acts.index', compact('acts'));
    }
}
