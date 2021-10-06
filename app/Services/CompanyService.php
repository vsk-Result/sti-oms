<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\Company;

class CompanyService
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function createCompany(array $requestData): void
    {
        Company::create([
            'name' => $this->sanitizer->set($requestData['name'])->get(),
            'short_name' => $this->sanitizer->set($requestData['short_name'])->get(),
            'inn' => $this->sanitizer->set($requestData['inn'])->toNumber()->get()
        ]);
    }

    public function updateCompany(Company $company, array $requestData): void
    {
        $company->update([
            'name' => $this->sanitizer->set($requestData['name'])->get(),
            'short_name' => $this->sanitizer->set($requestData['short_name'])->get(),
            'inn' => $this->sanitizer->set($requestData['inn'])->toNumber()->get(),
            'status_id' => $requestData['status_id']
        ]);
    }

    public function destroyCompany(Company $company): void
    {
        $company->delete();
    }
}
