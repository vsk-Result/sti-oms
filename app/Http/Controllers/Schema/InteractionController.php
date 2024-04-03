<?php

namespace App\Http\Controllers\Schema;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Schema\Interaction;
use App\Services\Schema\InteractionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InteractionController extends Controller
{
    private InteractionService $interactionService;

    public function __construct(InteractionService $interactionService)
    {
        $this->interactionService = $interactionService;
    }

    public function index(Request $request): View | JsonResponse
    {
        if ($request->ajax()) {
            $status = 'success';
            $companies = Interaction::getCompanies();
            $interactions = $this->interactionService->getInteractions();

            return response()->json(compact('status', 'interactions', 'companies'));
        }

        return view('schemas.interactions.index');
    }

    public function edit(Request $request): JsonResponse
    {
        $company = $request->get('company');
        $currencies = Currency::getCurrencies();
        $interactions = $this->interactionService->getInteractionsByCompany($company);
        $interactionsTable = view('schemas.interactions.partials._interaction_table', compact('interactions', 'currencies', 'company'))->render();

        return response()->json(['status' => 'success', 'interactions_table' => $interactionsTable]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->interactionService->updateInteraction($request->toArray());

        return redirect()->back();
    }
}
