<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Models\Status;
use App\Services\DepositService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    private DepositService $depositService;

    public function __construct(DepositService $depositService)
    {
        $this->depositService = $depositService;
    }

    public function index(BObject $object, Request $request): View
    {
        $requestData = array_merge(['object_id' => [$object->id]], $request->toArray());

        $total = [];
        $objects = BObject::orderBy('code')->get();
        $contracts = Contract::with('parent')->orderBy('name')->get();
        $deposits = $this->depositService->filterDeposit($requestData, $total);

        $statuses = [
            Status::STATUS_ACTIVE => 'Активен',
            Status::STATUS_BLOCKED => 'В архиве',
            Status::STATUS_DELETED => 'Удален'
        ];

        return view('objects.tabs.deposits', compact('statuses', 'object', 'deposits', 'objects', 'contracts', 'total'));
    }
}
