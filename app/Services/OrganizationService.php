<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\Organization;
use App\Models\Status;

class OrganizationService
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function getOrCreateOrganization(array $requestData): Organization
    {
        $requestData['name'] = $this->sanitizer->set($requestData['name'])->get();
        $requestData['inn'] = $this->sanitizer->set($requestData['inn'])->toNumber()->get();
        $requestData['kpp'] = $this->sanitizer->set($requestData['kpp'])->toNumber()->get();

        if (empty($requestData['inn'])) {
            $organization = Organization::where('name', $requestData['name'])->first();

            return $organization ?: $this->createOrganization($requestData);
        }

        $organization = Organization::where('inn', $requestData['inn'])->first();

        if ($organization) {
            return $organization;
        }

        $organization = Organization::where('name', $requestData['name'])->first();

        if ($organization) {
            $organization->update([
                'inn' => $requestData['inn'],
            ]);

            return $organization;
        }

        return $this->createOrganization($requestData);
    }

    public function createOrganization(array $requestData): Organization
    {
        if ($requestData['company_id'] === 'null') {
            $requestData['company_id'] = null;
        }
        $organization = Organization::create([
            'company_id' => $requestData['company_id'],
            'name' => $this->sanitizer->set($requestData['name'])->get(),
            'inn' => $this->sanitizer->set($requestData['inn'])->toNumber()->get(),
            'kpp' => $this->sanitizer->set($requestData['kpp'])->toNumber()->get(),
            'status_id' => Status::STATUS_ACTIVE,
            'nds_status_id' => $requestData['nds_status_id'] ?? Organization::NDS_STATUS_AUTO,
        ]);

        return $organization;
    }

    public function updateOrganization(Organization $organization, array $requestData): Organization
    {
        if ($requestData['company_id'] === 'null') {
            $requestData['company_id'] = null;
        }
        $organization->update([
            'company_id' => $requestData['company_id'],
            'name' => $this->sanitizer->set($requestData['name'])->get(),
            'inn' => $this->sanitizer->set($requestData['inn'])->toNumber()->get(),
            'kpp' => $this->sanitizer->set($requestData['kpp'])->toNumber()->get(),
            'status_id' => $requestData['status_id'],
            'nds_status_id' => $requestData['nds_status_id'] ?? Organization::NDS_STATUS_AUTO,
        ]);

        return $organization;
    }

    public function destroyOrganization(Organization $organization): Organization
    {
        $organization->delete();
        return $organization;
    }
}
