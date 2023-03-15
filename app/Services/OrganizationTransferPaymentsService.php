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

    public function transfer(UploadedFile $file): array
    {
        $result = [];
        $importData = Excel::toArray(new PaymentTransferImport(), $file);
        foreach ($importData['Организации'] as $index => $row) {

            if ($index === 0) continue;

            $organization = Organization::where('name', $row[0])->first();
            $newOrganization = Organization::where('name', $row[1])->first();

            if (! $organization || ! $newOrganization) {
                continue;
            }

            if ($organization->id === $newOrganization->id) {
                continue;
            }

            $organization->paymentsSend()->update([
                'organization_sender_id' => $newOrganization->id
            ]);

            $organization->paymentsReceive()->update([
                'organization_receiver_id' => $newOrganization->id
            ]);

            $organization->debts()->update([
                'organization_id' => $newOrganization->id
            ]);

            $organization->loans()->update([
                'organization_id' => $newOrganization->id
            ]);

            $organization->bankGuarantees()->update([
                'organization_id' => $newOrganization->id
            ]);

            $organization->guarantees()->update([
                'organization_id' => $newOrganization->id
            ]);

            $organization->objects()->update([
                'customer_id' => $newOrganization->id
            ]);

            $result[] = [
                'old' => $organization->name,
                'new' => $newOrganization->name,
            ];

            $this->organizationService->destroyOrganization($organization);
        }

        return $result;
    }
}