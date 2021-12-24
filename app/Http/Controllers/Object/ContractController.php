<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use Illuminate\Contracts\View\View;

class ContractController extends Controller
{
    public function index(BObject $object): View
    {
        $contracts = $object->contracts()->where('type_id', Contract::TYPE_MAIN)
            ->with('object', 'children', 'acts', 'avanses', 'avansesReceived', 'acts.payments', 'children.acts', 'children.avanses', 'children.avansesReceived', 'children.acts.payments')
            ->get();
        return view('objects.tabs.contracts', compact('object', 'contracts'));
    }
}
