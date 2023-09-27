<?php

namespace App\Http\Controllers;

use App\Services\CRONProcessService;
use Illuminate\View\View;

class CRONProcessController extends Controller
{
    private CRONProcessService $CRONProcessService;

    public function __construct(CRONProcessService $CRONProcessService)
    {
        $this->CRONProcessService = $CRONProcessService;
    }

    public function index(): View
    {
        $processes = $this->CRONProcessService->getCronProcesses();
        return view('cron-process.index', compact('processes'));
    }
}
