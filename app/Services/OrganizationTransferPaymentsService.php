<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\Organization\PaymentTransferImport;
use App\Models\Organization;

class OrganizationTransferPaymentsService
{
    private OrganizationService $organizationService;

    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    public function transfer(UploadedFile $file): string
    {
        $importData = Excel::toArray(new PaymentTransferImport(), $file);
        foreach ($importData['Организации'] as $index => $row) {

            if ($index === 0) continue;

            $organization = Organization::where('name', $row[0])->first();
            $newOrganizaition = Organization::where('name', $row[1])->first();

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

            $organization->loans()->update([
                'organization_id' => $newOrganizaition->id
            ]);

            $organization->bankGuarantees()->update([
                'organization_id' => $newOrganizaition->id
            ]);

            $organization->objects()->update([
                'customer_id' => $newOrganizaition->id
            ]);

            $this->organizationService->destroyOrganization($organization);
        }

        return 'ok';
    }
}