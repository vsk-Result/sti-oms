<?php

namespace App\Http\Controllers\Pivot\DTSTI;

use App\Exports\Pivot\DTSTI\Export;
use App\Http\Controllers\Controller;
use App\Services\DTSTIPivotService;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private DTSTIPivotService $DTSTIPivotService;

    public function __construct(DTSTIPivotService $DTSTIPivotService)
    {
        $this->DTSTIPivotService = $DTSTIPivotService;
    }

    public function store(): BinaryFileResponse
    {
        $pivot = $this->DTSTIPivotService->getPivot();
        return Excel::download(new Export($pivot), 'Долги ДТ_СТИ.xlsx');
    }
}
