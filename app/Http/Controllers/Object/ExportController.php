<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\ObjectBalanceExportService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private ObjectBalanceExportService $balanceExportService;

    public function __construct(ObjectBalanceExportService $balanceExportService)
    {
        $this->balanceExportService = $balanceExportService;
    }

    public function store(BObject $object): BinaryFileResponse
    {
        return $this->balanceExportService->download($object);
    }
}
