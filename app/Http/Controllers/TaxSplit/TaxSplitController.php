<?php

namespace App\Http\Controllers\TaxSplit;

use App\Http\Controllers\Controller;
use App\Imports\TaxSplit\TaxSplitImport;
use App\Services\SplitTaxPaymentsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class TaxSplitController extends Controller
{
    public function __construct(private SplitTaxPaymentsService $splitTaxPaymentsService) {}

    public function index(): View
    {
        $taxTypes = ['НДФЛ' => 'НДФЛ', 'Страховые взносы' => 'Страховые взносы'];
        $payments = [];

        return view('tax-split.index', compact('taxTypes', 'payments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $splitInfo = Excel::toArray(new TaxSplitImport(), $request->file('file'));

        if ($request->type_id === 'НДФЛ') {
            $preparedInfo = $this->splitTaxPaymentsService->prepareSplitInfoNDFL($splitInfo['Лист_1']);
        } else {
            $preparedInfo = $this->splitTaxPaymentsService->prepareSplitInfo($splitInfo['Лист_1']);
        }

        $splitError = $this->splitTaxPaymentsService->splitPayments(
            $request->get('payment_ids'),
            $request->get('split_date'),
            $preparedInfo,
            $request->file('file')->getClientOriginalName()
        );

        if (! is_null($splitError)) {
            session()->flash('status_error', $splitError);
        } else {
            session()->flash('status_success', 'Разбивка прошла успешно');
        }

        return redirect()->back();
    }
}
