<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\Guarantee;
use App\Models\GuaranteePayment;
use App\Models\Status;
use App\Models\Writeoff;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Currency;

class WriteoffService
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function filterWriteoff(array $requestData, array &$total = [], bool $needPaginate = true): LengthAwarePaginator|Collection
    {
        $query = Writeoff::query();

        if (! empty($requestData['period'])) {
            $period = explode(' - ', $requestData['period']);
            $query->whereBetween('date', [Carbon::parse($period[0]), Carbon::parse($period[1])]);
        }

        if (! empty($requestData['object_id'])) {
            $query->whereIn('object_id', $requestData['object_id']);
        }

        if (! empty($requestData['description'])) {
            $query->where('description', 'LIKE', '%' . $requestData['description'] . '%');
        }

        if (! empty($requestData['company_id'])) {
            $query->whereIn('company_id', $requestData['company_id']);
        }

        $perPage = 30;
        if (! empty($requestData['count_per_page'])) {
            $perPage = (int) preg_replace("/[^0-9]/", '', $requestData['count_per_page']);
        }

        $total['amount'] = (clone $query)->sum('amount');

        $query->with('company', 'object');
        $query->orderByDesc('date');

        return $needPaginate ? $query->paginate($perPage)->withQueryString() : $query->get();
    }

    public function createWriteoff(array $requestData): Writeoff
    {
        $writeoff = Writeoff::create([
            'company_id' => $requestData['company_id'],
            'object_id' => $requestData['object_id'],
            'crm_employee_uid' => $requestData['crm_employee_uid'],
            'description' => $this->sanitizer->set($requestData['description'])->upperCaseFirstWord()->get(),
            'date' => $requestData['date'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'status_id' => Status::STATUS_ACTIVE,
        ]);

        if (! empty($requestData['files'])) {
            foreach ($requestData['files'] as $file) {
                $writeoff->addMedia($file)->toMediaCollection();
            }
        }

        return $writeoff;
    }

    public function updateWriteoff(Writeoff $writeoff, array $requestData): Writeoff
    {
        $writeoff->update([
            'company_id' => $requestData['company_id'],
            'object_id' => $requestData['object_id'],
            'crm_employee_uid' => $requestData['crm_employee_uid'],
            'description' => $this->sanitizer->set($requestData['description'])->upperCaseFirstWord()->get(),
            'date' => $requestData['date'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'status_id' => $requestData['status_id'],
        ]);

        if (! empty($requestData['files'])) {
            foreach ($requestData['files'] as $file) {
                $writeoff->addMedia($file)->toMediaCollection();
            }
        }

        return $writeoff;
    }

    public function destroyWriteoff(Writeoff $writeoff): void
    {
        $writeoff->delete();
    }
}
