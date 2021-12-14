<?php

namespace App\Http\Controllers\Contract;

use App\Http\Controllers\Controller;
use App\Models\Contract\Contract;
use Illuminate\View\View;

class ContractController extends Controller
{
    public function index(): View
    {
        $contracts = Contract::all();
        return view('contracts.index', compact('contracts'));
    }
}
