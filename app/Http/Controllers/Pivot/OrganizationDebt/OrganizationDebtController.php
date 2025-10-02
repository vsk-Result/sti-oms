<?php

namespace App\Http\Controllers\Pivot\OrganizationDebt;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Services\Pivots\OrganizationDebts\OrganizationDebtService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizationDebtController extends Controller
{
    private OrganizationDebtService $organizationDebtService;

    public function __construct(OrganizationDebtService $organizationDebtService)
    {
        $this->organizationDebtService = $organizationDebtService;
    }

    public function index(Request $request): View
    {
        $options = [
            'organization_ids' => $request->get('organization_id', []),
            'need_cache' => $request->get('need_cash', 'yes') === 'yes'
        ];

        $pivot = $this->organizationDebtService->getPivot($options);

        $activeOrganizations = [];
        if (! empty($request->get('organization_id'))) {
            $activeOrganizations = Organization::whereIn('id', $request->get('organization_id'))->orderBy('name')->get();
        }

        return view('pivots.organization-debts.index', compact('pivot', 'activeOrganizations'));
    }
}