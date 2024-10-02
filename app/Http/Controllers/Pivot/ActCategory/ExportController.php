<?php

namespace App\Http\Controllers\Pivot\ActCategory;

use App\Exports\Pivot\ActCategory\Export;
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
        return Excel::download(new Export($this->actService), 'Отчет по категориям.xlsx');
    }
}
