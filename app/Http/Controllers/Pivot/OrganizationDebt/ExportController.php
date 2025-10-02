<?php

namespace App\Http\Controllers\Pivot\OrganizationDebt;

use App\Exports\Pivot\OrganizationDebt\Export;
use App\Http\Controllers\Controller;
use App\Services\Pivots\OrganizationDebts\OrganizationDebtService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function __construct(OrganizationDebtService $organizationDebtService)
    {
        $this->organizationDebtService = $organizationDebtService;
    }

    public function store(Request $request): BinaryFileResponse
    {
        $options = [
            'organization_ids' => $request->get('organization_id', []),
            'need_cache' => $request->get('need_cash', 'yes') === 'yes'
        ];

        $pivot = $this->organizationDebtService->getPivot($options);

        return Excel::download(
            new Export($pivot),
            'Долги контрагентов на ' . now()->format('d.m.Y') . '.xlsx'
        );
    }
}
