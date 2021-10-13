<?php

namespace App\Http\Controllers\Statement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Statement\StoreStatementRequest;
use App\Models\Bank;
use App\Models\Company;
use App\Models\Payment;
use App\Models\Statement;
use App\Models\Object\BObject;
use App\Services\StatementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class StatementController extends Controller
{
    private StatementService $statementService;

    public function __construct(StatementService $statementService)
    {
        $this->statementService = $statementService;
    }

    public function index(): View
    {
        $statements = Statement::with('company', 'createdBy')->orderByDesc('date')->orderByDesc('id')->get();
        return view('statements.index', compact('statements'));
    }

    public function create(): View
    {
        $companies = Company::all();
        $banks = Bank::getBanks();
        return view('statements.create', compact('companies', 'banks'));
    }

    public function store(StoreStatementRequest $request): RedirectResponse
    {
        $statement = $this->statementService->createStatement($request->toArray());
        return redirect()->route('statements.edit', $statement);
    }

    public function show(Statement $statement): View
    {
        $statement->load(['payments', 'payments.organizationSender', 'payments.organizationReceiver']);
        return view('statements.show', compact('statement'));
    }

    public function edit(Statement $statement): View
    {
        $categories = Payment::getCategories();
        $objects = Payment::getTypes() + BObject::getObjectsList();
        $statement->load([
            'payments' => function($query) {
                $query->orderByDesc('amount');
            },
            'payments.organizationSender',
            'payments.organizationReceiver'
        ]);
        return view('statements.edit', compact('statement', 'objects', 'categories'));
    }

    public function destroy(Statement $statement): RedirectResponse
    {
        $this->statementService->destroyStatement($statement);
        return redirect()->route('statements.index');
    }
}
