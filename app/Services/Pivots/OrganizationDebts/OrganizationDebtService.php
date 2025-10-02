<?php

namespace App\Services\Pivots\OrganizationDebts;

use App\Services\PivotObjectDebtService;
use Illuminate\Support\Facades\Cache;

class OrganizationDebtService
{
    private PivotObjectDebtService $pivotObjectDebtService;

    public function __construct(PivotObjectDebtService $pivotObjectDebtService)
    {
        $this->pivotObjectDebtService = $pivotObjectDebtService;
    }

    public function getPivot(array $options): array
    {
        $needCache = $options['need_cache'] ?? true;

        if (isset($options['organization_ids']) && count($options['organization_ids']) > 0) {
            $needCache = false;
        }

        $service = $this->pivotObjectDebtService;

        if ($needCache) {
            return Cache::remember('pivot_organization_debts', now()->addHour(), function() use ($service, $options) {
                return $service->getPivotDebtsForOrganizations($options);
            });
        }

        return $this->pivotObjectDebtService->getPivotDebtsForOrganizations($options);
    }
}