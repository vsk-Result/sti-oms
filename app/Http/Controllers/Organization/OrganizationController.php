<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\StoreOrUpdateOrganizationRequest;
use App\Models\Company;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\Status;
use App\Services\OrganizationService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class OrganizationController extends Controller
{
    private OrganizationService $organizationService;
    private PaymentService $paymentService;

    public function __construct(OrganizationService $organizationService, PaymentService $paymentService)
    {
        $this->organizationService = $organizationService;
        $this->paymentService = $paymentService;
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {

            if (empty($request->get('search')) && $request->get('type') === 'select') {
                return response()->json();
            }

            $query = Organization::query();
            $objectIds = $request->get('objects');
            $search = $request->get('search');

            $query->where(function($q) use($search) {
                $q->where('name', 'LIKE', '%' . $search . '%');
                $q->orWhere('inn', 'LIKE', '%' . $search . '%');
            });


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

            if ($request->get('type') === 'select') {
                $organizations = [];

                foreach ($query->orderBy('name')->get() as $item) {
                    if (! empty($item->inn)) {
                        $organizations[$item->id] = $item->name . ', ' . $item->inn;
                    } else {
                        $organizations[$item->id] = $item->name;
                    }
                }
                return response()->json(compact('organizations'));
            }

            $organizations = $query->orderBy('name')->paginate(20)->withQueryString();
            return response()->json([
                'status' => 'success',
                'organizations_view' => view('organizations.parts._organizations', compact('organizations'))->render()
            ]);
        }

        $organizations = Organization::orderBy('name')->paginate(30);

        return view('organizations.index', compact('organizations'));
    }

    public function create(): View
    {
        $NDSStatuses = Organization::getNDSStatuses();
        $companies = Company::orderBy('id')->get();
        $categories = Payment::getCategories();
        return view('organizations.create', compact('companies', 'NDSStatuses', 'categories'));
    }

    public function store(StoreOrUpdateOrganizationRequest $request): RedirectResponse
    {
        $this->organizationService->createOrganization($request->toArray());
        return redirect()->route('organizations.index');
    }

    public function edit(Organization $organization): View
    {
        $statuses = Status::getStatuses();
        $NDSStatuses = Organization::getNDSStatuses();
        $companies = Company::orderBy('id')->get();
        $categories = Payment::getCategories();
        return view('organizations.edit', compact('organization', 'companies', 'statuses', 'NDSStatuses', 'categories'));
    }

    public function update(Organization $organization, StoreOrUpdateOrganizationRequest $request): RedirectResponse
    {
        $this->organizationService->updateOrganization($organization, $request->toArray());

        foreach ($organization->paymentsSend as $payment) {
            $this->paymentService->updatePayment($payment, ['amount' => $payment->amount]);
        }

        foreach ($organization->paymentsReceive as $payment) {
            $this->paymentService->updatePayment($payment, ['amount' => $payment->amount]);
        }

        return redirect()->route('organizations.index');
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        $this->organizationService->destroyOrganization($organization);
        return redirect()->route('organizations.index');
    }
}
