<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\StoreOrUpdateOrganizationRequest;
use App\Models\Company;
use App\Models\Organization;
use App\Models\Status;
use App\Services\OrganizationService;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class OrganizationController extends Controller
{
    private OrganizationService $organizationService;

    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    public function index(): View
    {
        $organizations = Organization::orderBy('name')->get();
        return view('organizations.index', compact('organizations'));
    }

    public function create(): View
    {
        $companies = Company::orderBy('name')->get();
        return view('organizations.create', compact('companies'));
    }

    public function store(StoreOrUpdateOrganizationRequest $request): RedirectResponse
    {
        $this->organizationService->createOrganization($request->toArray());
        return redirect()->route('organizations.index');
    }

    public function edit(Organization $organization): View
    {
        $statuses = Status::getStatuses();
        $companies = Company::orderBy('name')->get();
        return view('organizations.edit', compact('organization', 'companies', 'statuses'));
    }

    public function update(Organization $organization, StoreOrUpdateOrganizationRequest $request): RedirectResponse
    {
        $this->organizationService->updateOrganization($organization, $request->toArray());
        return redirect()->route('organizations.index');
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        $this->organizationService->destroyOrganization($organization);
        return redirect()->route('organizations.index');
    }
}
