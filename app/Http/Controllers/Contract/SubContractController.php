<?php

namespace App\Http\Controllers\Contract;

use App\Http\Controllers\Controller;
use App\Models\Contract\Contract;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class SubContractController extends Controller
{
    public function index(Contract $contract): View|JsonResponse
    {
        if (request()->ajax()) {
            $currency = request()->get('currency', '');
            $contracts = $contract->children()->where('currency', $currency)->with('object', 'acts', 'avanses', 'avansesReceived', 'acts.payments')->get();
            $mainContract = $contract;
            return response()->json([
                'status' => 'success',
                'contracts_view' => view('contracts.parts._contracts', compact('contracts', 'mainContract', 'currency'))->render()
            ]);
        }
        $subcontracts = $contract->children()->with('object', 'acts', 'avanses', 'avansesReceived', 'acts.payments')->get();
        return view('contracts.tabs.subcontracts', compact('contract', 'subcontracts'));
    }
}
