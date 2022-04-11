<?php

namespace App\Http\Controllers\Organization;

use App\Helpers\Sanitizer;
use App\Http\Controllers\Controller;
use App\Imports\Organization\PaymentTransferImport;
use App\Models\Debt\Debt;
use App\Models\Organization;
use App\Services\OrganizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TransferPaymentImportController extends Controller
{
    private Sanitizer $sanitizer;
    private OrganizationService $organizationService;

    public function __construct(Sanitizer $sanitizer, OrganizationService $organizationService)
    {
        $this->sanitizer = $sanitizer;
        $this->organizationService = $organizationService;
    }

    public function store(Request $request): RedirectResponse
    {
        $requestData = $request->toArray();
        $importData = Excel::toArray(new PaymentTransferImport(), $requestData['file']);
        foreach ($importData['Организации'] as $index => $row) {
            if ($index === 0) continue;

            $newOrganizaition = Organization::where('name', $row[2])->first();

            if (! $newOrganizaition) {
                continue;
            }

            Debt::where('organization_id', $row[0])->update([
                'organization_id' => $newOrganizaition->id
            ]);

            continue;

            $organization = Organization::find($row[0]);


            if (! $organization || ! $newOrganizaition) {
                continue;
            }

            $organization->paymentsSend()->update([
                'organization_sender_id' => $newOrganizaition->id
            ]);

            $organization->paymentsReceive()->update([
                'organization_receiver_id' => $newOrganizaition->id
            ]);

            $organization->debts()->update([
                'organization_id' => $newOrganizaition->id
            ]);

            $this->organizationService->destroyOrganization($organization);
        }

        return redirect()->back();
    }
}
