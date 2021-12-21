<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\StoreOrUpdateOrganizationRequest;
use App\Models\Company;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\Status;
use App\Services\OrganizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class OrganizationController extends Controller
{
    private OrganizationService $organizationService;

    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax() && ! empty($request->get('search'))) {
            $query = Organization::query();
            $objectIds = $request->get('objects');

            $query->where('name', 'LIKE', '%' . $request->get('search') . '%');

            if (! empty($objectIds)) {
                $organizationsIds = [];

                $payments = Payment::where(function($q) use($objectIds) {
                    $q->whereIn('object_id', $objectIds);
                });

                $organizationsIds = array_merge(
                    $organizationsIds,
                    $payments->pluck('organization_sender_id')->toArray(),
                    $payments->pluck('organization_receiver_id')->toArray()
                );

                $organizationsIds = array_unique($organizationsIds);
                if (! empty($organizationsIds)) {
                    $query->whereIn('id', $organizationsIds);
                }
            }

            $organizations = $query->orderBy('name')->pluck('name', 'id');
            return response()->json(compact('organizations'));
        }

        $organizations = Organization::orderBy('name')->paginate(30);

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
