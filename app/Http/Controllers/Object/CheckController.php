<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\ObjectCheckService;
use Illuminate\Contracts\View\View;

class CheckController extends Controller
{
    private ObjectCheckService $objectCheckService;

    public function __construct(ObjectCheckService $objectCheckService)
    {
        $this->objectCheckService = $objectCheckService;
    }

    public function index(BObject $object): View
    {
        $checkInfo = $this->objectCheckService->getCheckStatusInfo($object);
        return view('objects.tabs.check', compact('checkInfo', 'object'));
    }
}
