<?php

namespace App\Http\Controllers\Debt;

use App\Http\Controllers\Controller;
use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\Object\BObject;
use App\Models\Object\WorkType;
use App\Models\Organization;
use App\Services\DebtService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    private DebtService $debtService;

    public function __construct(DebtService $debtService)
    {
        $this->debtService = $debtService;
    }

    public function index(Request $request): View
    {
        $total = [];
        $types = Debt::getTypes();
        $objects = BObject::orderBy('code')->get();
        $workTypes = WorkType::getWorkTypes();
        $organizations = Organization::orderBy('name')->get();
        $categories = ['Аванс', 'Акт', 'Материалы', 'Транспорт'];
        $debts = $this->debtService->filterDebts($request->toArray(), $total);
        $imports = DebtImport::orderByDesc('date')->get();

        return view('debts.index', compact(
            'debts', 'total', 'types',
            'categories', 'objects', 'imports', 'organizations', 'workTypes'
        ));
    }
}
