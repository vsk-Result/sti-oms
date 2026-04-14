<?php

namespace App\Http\Controllers\Finance;

use App\Exports\Finance\GeneralCosts\Export;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function store(Request $request): BinaryFileResponse
    {
        $requestYears = request()->input('year', []);
        $requestObjects = request()->input('object_id', []);
        $filterNDS = $request->get('filter_nds', 'nds');

        return Excel::download(
            new Export($requestYears, $requestObjects, $filterNDS),
            'Общие затраты на ' . Carbon::now()->format('d-m-Y') . '.xlsx'
        );
    }
}
