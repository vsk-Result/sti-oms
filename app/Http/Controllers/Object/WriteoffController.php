<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Http\Requests\Object\StoreOrUpdateObjectRequest;
use App\Models\Company;
use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Services\GuaranteeService;
use App\Services\WriteoffService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class WriteoffController extends Controller
{
    private WriteoffService $writeoffService;

    public function __construct(WriteoffService $writeoffService)
    {
        $this->writeoffService = $writeoffService;
    }

    public function index(BObject $object, Request $request): View
    {
        $requestData = array_merge(['object_id' => [$object->id]], $request->toArray());

        $total = [];
        $objects = BObject::orderBy('code')->get();

        if (auth()->user()->hasRole(['object-leader', 'finance-object-user'])) {
            $objects = BObject::whereIn('id', auth()->user()->objects->pluck('id'))->orderBy('code')->get();
        }

        $companies = Company::orderBy('name')->get();

        $totalInfo = [];
        $writeoffs = $this->writeoffService->filterWriteoff($requestData, $totalInfo);

        return view('objects.tabs.writeoffs', compact('object', 'objects', 'companies', 'writeoffs', 'totalInfo'));
    }
}
