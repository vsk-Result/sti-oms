<?php

namespace App\Http\Controllers\Contract;

use App\Http\Controllers\Controller;
use App\Models\Contract\Contract;
use Illuminate\View\View;

class ActController extends Controller
{
    public function index(Contract $contract): View
    {
        $acts = $contract->acts()->with('payments')->get();
        return view('contracts.tabs.acts', compact('contract', 'acts'));
    }
}
