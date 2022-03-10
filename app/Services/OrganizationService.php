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

        $organization = Organization::where('name', $requestData['name'])->first();

        return $organization ?: $this->createOrganization($requestData);
    }

    public function createOrganization(array $requestData): Organization
    {
        $organization = Organization::create([
            'company_id' => $requestData['company_id'],
            'name' => $this->sanitizer->set($requestData['name'])->get(),
            'inn' => $this->sanitizer->set($requestData['inn'])->toNumber()->get(),
            'kpp' => $this->sanitizer->set($requestData['kpp'])->toNumber()->get(),
            'status_id' => Status::STATUS_ACTIVE,
            'nds_status_id' => $requestData['nds_status_id'],
        ]);

        return $organization;
    }

    public function updateOrganization(Organization $organization, array $requestData): Organization
    {
        $organization->update([
            'company_id' => $requestData['company_id'],
            'name' => $this->sanitizer->set($requestData['name'])->get(),
            'inn' => $this->sanitizer->set($requestData['inn'])->toNumber()->get(),
            'kpp' => $this->sanitizer->set($requestData['kpp'])->toNumber()->get(),
            'status_id' => $requestData['status_id'],
            'nds_status_id' => $requestData['nds_status_id'],
        ]);

        return $organization;
    }

    public function destroyOrganization(Organization $organization): Organization
    {
        $organization->delete();
        return $organization;
    }
}
