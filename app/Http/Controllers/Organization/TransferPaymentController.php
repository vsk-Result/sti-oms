<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Services\OrganizationService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TransferPaymentController extends Controller
{
    private OrganizationService $organizationService;
    private PaymentService $paymentService;

    public function __construct(OrganizationService $organizationService, PaymentService $paymentService)
    {
        $this->organizationService = $organizationService;
        $this->paymentService = $paymentService;
    }

    public function create(Organization $organization): View
    {
        $organizations = Organization::where('id', '!=', $organization->id)->orderBy('name')->get();
        return view('organizations.transfer-payments.create', compact('organization', 'organizations'));
    }

    public function store(Organization $organization, Request $request): RedirectResponse
    {
        $newOrganizationId = $request->input('organization_id');

        $organization->paymentsSend()->update([
            'organization_sender_id' => $newOrganizationId
        ]);

        $organization->paymentsReceive()->update([
            'organization_receiver_id' => $newOrganizationId
        ]);

        $organization->debts()->update([
            'organization_id' => $newOrganizationId
        ]);

        $organization->loans()->update([
            'organization_id' => $newOrganizationId
        ]);

        $organization->bankGuarantees()->update([
            'organization_id' => $newOrganizationId
        ]);

        $organization->objects()->update([
            'customer_id' => $newOrganizationId
        ]);

        $this->organizationService->destroyOrganization($organization);

        return redirect()->route('organizations.index');
    }
}
