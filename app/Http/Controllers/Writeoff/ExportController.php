<?php

namespace App\Http\Controllers\Writeoff;

use App\Exports\Writeoff\Export;
use App\Http\Controllers\Controller;
use App\Services\WriteoffService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private WriteoffService $writeoffService;

    public function __construct(WriteoffService $writeoffService)
    {
        $this->writeoffService = $writeoffService;
    }

    public function store(Request $request): BinaryFileResponse
    {
        $totalInfo = [];
        $writeoffs = $this->writeoffService->filterWriteoff($request->toArray(), $totalInfo, false);

        return Excel::download(new Export($writeoffs), 'Экспорт списаний.xlsx');
    }
}
