<?php
namespace App\Http\Controllers\Object\Report\ActCategory;

use App\Exports\Object\Report\ActCategory\Export;
use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\Contract\ActService;
use App\Services\CurrencyExchangeRateService;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private ActService $actService;
    private CurrencyExchangeRateService $currencyExchangeService;

    public function __construct(ActService $actService, CurrencyExchangeRateService $currencyExchangeService)
    {
        $this->actService = $actService;
        $this->currencyExchangeService = $currencyExchangeService;
    }

    public function store(BObject $object): BinaryFileResponse
    {
        return Excel::download(new Export($this->actService, $object, $this->currencyExchangeService), 'Отчет по категориям.xlsx');
    }
}
