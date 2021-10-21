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
        foreach ($organization->paymentsSend as $payment) {
            $this->paymentService->updatePayment(
                $payment,
                ['organization_sender_id' => $request->input('organization_id')]
            );
        }

        foreach ($organization->paymentsReceive as $payment) {
            $this->paymentService->updatePayment(
                $payment,
                ['organization_receiver_id' => $request->input('organization_id')]
            );
        }

        $this->organizationService->destroyOrganization($organization);

        return redirect()->route('organizations.index');
    }
}
