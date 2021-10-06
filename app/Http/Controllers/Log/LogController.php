<?php

namespace App\Http\Controllers\Log;

use App\Http\Controllers\Controller;
use App\Services\LogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LogController extends Controller
{
    private LogService $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    public function index(): View
    {
        $logs = $this->logService->getLogs();
        return view('logs.index', compact('logs'));
    }

    public function update(string $log): RedirectResponse|StreamedResponse
    {
        return $this->logService->downloadLog($log);
    }

    public function show(string $log, Request $request)
    {
        $logDetails = $this->logService->getLogDetails($log, $request->get('count', 10));
        return view('logs.show', compact('log', 'logDetails'));
    }
}
