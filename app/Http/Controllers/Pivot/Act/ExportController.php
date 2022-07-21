<?php

namespace App\Http\Controllers\Pivot\Act;

use App\Exports\Pivot\Act\Export;
use App\Http\Controllers\Controller;
use App\Services\Contract\ActService;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private ActService $actService;

    public function __construct(ActService $actService)
    {
        $this->actService = $actService;
    }

    public function store(): BinaryFileResponse
    {
        $pivot = $this->actService->getPivot();
        return Excel::download(new Export($pivot), 'Долги к СТИ.xlsx');
    }
}
