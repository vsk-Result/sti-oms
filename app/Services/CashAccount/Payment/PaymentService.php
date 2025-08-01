<?php

namespace App\Services\CashAccount\Payment;

use App\Helpers\Sanitizer;
use App\Models\CashAccount\CashAccountPayment;
use App\Models\CRM\Avans;
use App\Models\Object\BObject;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentService
{
    public function __construct(private Sanitizer $sanitizer) {}

    public function filterPayments(array $requestData, bool $needPaginate = false, array &$totalInfo = []): Builder|LengthAwarePaginator
    {
        $paymentQuery = CashAccountPayment::query();

        if (! empty($requestData['cash_account_id'])) {
            $paymentQuery->whereIn('cash_account_id', $requestData['cash_account_id']);
        }

        if (! empty($requestData['sort_by'])) {
            if ($requestData['sort_by'] == 'organization_id') {
                $paymentQuery->orderBy(Organization::select('name')->whereColumn('organizations.id', 'payments.organization_receiver_id')->orWhereColumn('organizations.id', 'payments.organization_sender_id'), $requestData['sort_direction'] ?? 'asc');
            } elseif ($requestData['sort_by'] == 'object_id') {
                $paymentQuery->orderBy('type_id', $requestData['sort_direction'] ?? 'asc');
                $paymentQuery->orderBy(BObject::select('code')->whereColumn('objects.id', 'payments.object_id'), $requestData['sort_direction'] ?? 'asc');
                $paymentQuery->orderBy('object_worktype_id', $requestData['sort_direction'] ?? 'asc');
            } else {
                $paymentQuery->orderBy($requestData['sort_by'], $requestData['sort_direction'] ?? 'asc');
            }
        } else {
            $paymentQuery->orderByDesc('date')
                ->orderByDesc('id');
        }

//        $paymentQuery->with('company', 'import', 'createdBy', 'object', 'audits', 'organizationSender', 'organizationReceiver');

        if ($needPaginate) {
            $perPage = 30;

            if (! empty($requestData['count_per_page'])) {
                $perPage = (int) preg_replace("/[^0-9]/", '', $requestData['count_per_page']);
            }

            $totalInfo['amount_pay'] = (clone $paymentQuery)->where('amount', '<', 0)->sum('amount');
            $totalInfo['amount_receive'] = (clone $paymentQuery)->where('amount', '>=', 0)->sum('amount');

            return $paymentQuery->paginate($perPage)->withQueryString();
        }

        return $paymentQuery;
    }

    public function createPayment(array $requestData): CashAccountPayment
    {
        $paymentCurrency = 'RUB';
        $paymentCurrencyRate = 1;

        $description = $this->sanitizer->set($requestData['description'] ?? '')->upperCaseFirstWord()->get();
        $amount = $this->sanitizer->set($requestData['amount'])->toAmount()->get();
        $crmEmployeeId = null;
        $crmDate = null;

        $isCrmEmployee = $requestData['code'] === '7.8.2' || $requestData['code'] === '7.9.2';

        if ($isCrmEmployee) {
            $crmDate = isset($requestData['crm_date']) ? get_date_and_month_from_string($requestData['crm_date'], true) : null;
            $crmEmployeeId = $requestData['crm_employee_id'] ?? null;
            $organizationId = Organization::where('company_id', 1)->first()?->id ?? null;
//
//            if ($requestData['code'] === '7.8.2') {
//                $description = $description . ', Выплата аванса рабочему - ' . Employee::find($crmEmployeeId)?->getFullname() . ' за ' . $crmDate;
//            } else {
//                $description = $description . ', Выплата зарплаты рабочему - ' . Employee::find($crmEmployeeId)?->getFullname() . ' за ' . $crmDate;
//            }
        } else {
            $organizationId = $requestData['organization_id'];
        }

        $objectId = (int) substr($requestData['object_id'], 0, strpos($requestData['object_id'], '::'));
        $objectWorktypeId = substr($requestData['object_id'], strpos($requestData['object_id'], '::') + 2);

        $payment = CashAccountPayment::create([
            'cash_account_id' => $requestData['cash_account_id'],
            'object_id' => $objectId,
            'object_worktype_id' => $objectWorktypeId,
            'organization_id' => $organizationId,
            'type_id' => $requestData['type_id'] ?? CashAccountPayment::TYPE_OBJECT,
            'category' => $requestData['category'],
            'code' => $requestData['code'],
            'description' => $description,
            'date' => $requestData['date'],
            'amount' => $amount,
            'status_id' => $requestData['status_id'] ?? CashAccountPayment::STATUS_ACTIVE,
            'currency' => $paymentCurrency,
            'currency_rate' => $paymentCurrencyRate,
            'currency_amount' => $amount,
            'crm_employee_id' => $crmEmployeeId,
            'crm_date' => $crmDate,
        ]);

        if (! empty($requestData['files'])) {
            foreach ($requestData['files'] as $file) {
                if (! is_null($file)) {
                    $payment->addMedia($file)->toMediaCollection();
                }
            }
        }

        if ($isCrmEmployee) {
            $this->createCRMEntry($payment);
        }

        return $payment;
    }

    public function updatePayment(CashAccountPayment $payment, array $requestData): void
    {
        $description = $this->sanitizer->set($requestData['description'] ?? '')->upperCaseFirstWord()->get();
        $amount = $this->sanitizer->set($requestData['amount'])->toAmount()->get();
        $crmEmployeeId = null;
        $crmDate = null;

        $isCrmEmployee = $requestData['code'] === '7.8.2' || $requestData['code'] === '7.9.2';

        if ($isCrmEmployee) {
            $crmDate = isset($requestData['crm_date']) ? get_date_and_month_from_string($requestData['crm_date'], true) : null;
            $crmEmployeeId = $requestData['crm_employee_id'] ?? null;
            $organizationId = Organization::where('company_id', 1)->first()?->id ?? null;
        } else {
            $organizationId = $requestData['organization_id'];
        }

        $objectId = (int) substr($requestData['object_id'], 0, strpos($requestData['object_id'], '::'));
        $objectWorktypeId = substr($requestData['object_id'], strpos($requestData['object_id'], '::') + 2);

        $needCrateCrmAvans = is_null($payment->crm_avans_id) && $isCrmEmployee;
        $needUpdateCrmAvans = !is_null($payment->crm_avans_id) && $isCrmEmployee;
        $needDeleteCrmAvans = !is_null($payment->crm_avans_id) && !$isCrmEmployee;

        $payment->update([
            'object_id' => $objectId,
            'object_worktype_id' => $objectWorktypeId,
            'organization_id' => $organizationId,
            'type_id' => $requestData['type_id'] ?? CashAccountPayment::TYPE_OBJECT,
            'category' => $requestData['category'],
            'code' => $requestData['code'],
            'description' => $description,
            'date' => $requestData['date'],
            'amount' => $amount,
            'status_id' => $requestData['status_id'] ?? $payment->status_id,
            'currency_amount' => $amount,
            'crm_employee_id' => $crmEmployeeId,
            'crm_date' => $crmDate,
        ]);

        if (! empty($requestData['files'])) {
            foreach ($requestData['files'] as $file) {
                if (! is_null($file)) {
                    $payment->addMedia($file)->toMediaCollection();
                }
            }
        }

        if ($needCrateCrmAvans) {
            $this->createCRMEntry($payment);
        }

        if ($needUpdateCrmAvans) {
            $this->updateCRMEntry($payment);
        }

        if ($needDeleteCrmAvans) {
            $this->deleteCRMEntry($payment);
        }
    }

    public function destroyPayment(CashAccountPayment $payment): void
    {
        if (! is_null($payment->crm_avans_id)) {
            $this->deleteCRMEntry($payment);
        }

        $payment->delete();
    }

    public function createCRMEntry(CashAccountPayment $payment): void
    {
        $avans = new Avans;
        $avans->u_id = auth()->user()->crm_user_id;
        $avans->e_id = $payment->crm_employee_id;
        $avans->type = 'Затраты';
        $avans->date = $payment->crm_date;
        $avans->code = $payment->getObjectCode();
        $avans->issue_date = Carbon::now();
        $avans->user_change_id = auth()->user()->crm_user_id;
        $avans->updated_at = Carbon::now();
        $avans->value = abs($payment->amount);
        $avans->save();

        $payment->update([
            'crm_avans_id' => $avans->id
        ]);
    }

    public function updateCRMEntry(CashAccountPayment $payment): void
    {
        $avans = Avans::find($payment->crm_avans_id);

        if ($avans) {
            $avans->e_id = $payment->crm_employee_id;
            $avans->date = $payment->crm_date;
            $avans->code = $payment->getObjectCode();
            $avans->user_change_id = auth()->user()->crm_user_id;
            $avans->updated_at = Carbon::now();
            $avans->value = abs($payment->amount);
            $avans->update();
        }
    }

    public function deleteCRMEntry(CashAccountPayment $payment): void
    {
        $avans = Avans::find($payment->crm_avans_id);

        if ($avans) {
            $avans->delete();
            $payment->update([
                'crm_avans_id' => null
            ]);
        }
    }
}
