<?php

namespace App\Http\Controllers\Finance\FinanceReport;

use App\Http\Controllers\Controller;
use App\Models\FinanceReportHistory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use function view;

class FinanceReportHistoryController extends Controller
{
    public function index(): View
    {
        $financeReportHistoryItems = FinanceReportHistory::orderByDesc('id')->paginate(15);

        return view(
            'finance-report.history.index',
            compact('financeReportHistoryItems')
        );
    }
}
