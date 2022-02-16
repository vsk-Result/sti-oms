<?php

namespace App\Http\Controllers\Contract;

use App\Http\Controllers\Controller;
use App\Models\Contract\Contract;
use Illuminate\View\View;

class SubContractController extends Controller
{
    public function index(Contract $contract): View
    {
        $subcontracts = $contract->children()->with('object', 'acts', 'avanses', 'avansesReceived', 'acts.payments')->get();
        return view('contracts.tabs.subcontracts', compact('contract', 'subcontracts'));
    }
}
