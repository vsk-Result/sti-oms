<?php

namespace App\Http\Controllers\Pivot\ActCategory;

use App\Exports\Pivot\ActCategory\Export;
use App\Http\Controllers\Controller;
use App\Services\Contract\ActService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function __construct(private ActService $actService) {}

    public function store(Request $request): BinaryFileResponse
    {
        return Excel::download(new Export($this->actService, $request->all()), 'Отчет по категориям.xlsx');
    }
}
