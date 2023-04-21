<?php

namespace App\Http\Controllers\Pivot\DTSTI;

use App\Http\Controllers\Controller;
use App\Services\Contract\ActService;
use App\Services\DTSTIPivotService;
use Illuminate\View\View;
use function view;

class DTSTIController extends Controller
{
    private DTSTIPivotService $DTSTIPivotService;

    public function __construct(DTSTIPivotService $DTSTIPivotService)
    {
        $this->DTSTIPivotService = $DTSTIPivotService;
    }

    public function index(): View
    {
        $pivot = $this->DTSTIPivotService->getPivot();
        return view('pivots.dtsti.index', compact('pivot'));
    }
}
