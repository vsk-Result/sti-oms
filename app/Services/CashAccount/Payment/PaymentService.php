<?php

namespace App\Services\CashAccount\Payment;

use App\Helpers\Sanitizer;
use App\Models\CashAccount\CashAccountPayment;
use App\Models\CRM\Apartment;
use App\Models\CRM\ApartmentPayment;
use App\Models\CRM\Avans;
use App\Models\CRM\Employee;
use App\Models\Object\BObject;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class PaymentService
{
    public function __construct(private Sanitizer $sanitizer) {}

    public function filterPayments(array $requestData, bool $needPaginate = false, array &$totalInfo = []): Builder|LengthAwarePaginator
    {
        $paymentQuery = CashAccountPayment::query();

        if (! empty($requestData['period'])) {
            $period = explode(' - ', $requestData['period']);
            $paymentQuery->whereBetween('date', [Carbon::parse($period[0]), Carbon::parse($period[1])]);
        }

        if (! empty($requestData['year']) && ! empty($requestData['month'])) {
            $paymentQuery->where('date', 'LIKE', $requestData['year'] . '-' . $requestData['month'] . '%');
        }

        if (! empty($requestData['description'])) {
            $descriptionORTags = explode('%%', $requestData['description']);
            $descriptionANDTags = explode('^^', $requestData['description']);

            if (count($descriptionORTags) > 1) {
                $paymentQuery->where(function($q) use ($descriptionORTags) {
                    foreach ($descriptionORTags as $tag) {
                        $q->orWhere('description', 'LIKE', '%' . $tag . '%');
                    }
                });
            } else if (count($descriptionANDTags) > 1) {
                $paymentQuery->where(function($q) use ($descriptionANDTags) {
                    foreach ($descriptionANDTags as $tag) {
                        $q->where('description', 'LIKE', '%' . $tag . '%');
                    }
                });
            } else {
                $paymentQuery->where('description', 'LIKE', '%' . $descriptionORTags[0] . '%');
            }
        }

        if (! empty($requestData['organization_id'])) {
            $paymentQuery->whereIn('organization_id',$requestData['organization_id']);
        }

        if (! empty($requestData['object_id'])) {
            $paymentQuery->whereIn('object_id',$requestData['object_id']);
        }

        if (! empty($requestData['object_worktype_id'])) {
            $paymentQuery->whereIn('object_worktype_id', $requestData['object_worktype_id']);
        }

        if (! empty($requestData['category'])) {
            $paymentQuery->whereIn('category', $requestData['category']);
        }

        if (! empty($requestData['status_id'])) {
            $paymentQuery->whereIn('status_id', $requestData['status_id']);
        } else {
            $paymentQuery->whereIn('status_id', [CashAccountPayment::STATUS_ACTIVE, CashAccountPayment::STATUS_VALID, CashAccountPayment::STATUS_WAITING, CashAccountPayment::STATUS_VALIDATED]);
        }

        if (! empty($requestData['amount_expression_operator']) && isset($requestData['amount_expression'])) {
            $expressionAmount = str_replace(',', '.', $requestData['amount_expression']);
            $expressionAmount = preg_replace("/[^-.0-9]/", '', $expressionAmount);

            $paymentQuery->where('amount', $requestData['amount_expression_operator'], $expressionAmount);
        }

        if (! empty($requestData['code'])) {
            if (in_array('null', $requestData['code'])) {
                array_push($requestData['code'], null, '');
            }
            $paymentQuery->whereIn('code', $requestData['code']);
        }

        if (! empty($requestData['cash_account_id'])) {
            $paymentQuery->whereIn('cash_account_id', $requestData['cash_account_id']);
        }

        if (! empty($requestData['sort_by'])) {
            if ($requestData['sort_by'] == 'organization_id') {
                $paymentQuery->orderBy(Organization::select('name')->whereColumn('organizations.id', 'cash_account_payments.organization_id'), $requestData['sort_direction'] ?? 'asc');
            } elseif ($requestData['sort_by'] == 'type') {
                $paymentQuery->orderBy('type_id', $requestData['sort_direction'] ?? 'asc');
            } elseif ($requestData['sort_by'] == 'object_id') {
                $paymentQuery->orderBy('type_id', $requestData['sort_direction'] ?? 'asc');
                $paymentQuery->orderBy(BObject::select('code')->whereColumn('objects.id', 'cash_account_payments.object_id'), $requestData['sort_direction'] ?? 'asc');
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

        $isReceiveAmount = isset($requestData['is_receive_amount']);
        $description = $this->sanitizer->set($requestData['description'] ?? '')->upperCaseFirstWord()->get();
        $amount = $this->sanitizer->set($requestData['amount'])->toAmount()->get();

        $typeId = $requestData['type_id'] ?? CashAccountPayment::TYPE_OBJECT;
        if ($typeId === CashAccountPayment::TYPE_OBJECT) {
            $amount = abs($amount);

            if (!$isReceiveAmount) {
                $amount = -$amount;
            }
        }

        $crmNotNeedAvans = isset($requestData['crm_not_need_avans']);

        $needCreateCrmAvans = $requestData['code'] === '7.8.2' || $requestData['code'] === '7.9.2';

        $isNotNeedCreateItr = isset($requestData['1c_itr_not_need_create']);
        $needCreateItr = ($requestData['code'] === '7.8.1' || $requestData['code'] === '7.9.1' || $requestData['code'] === '7.10');

        $needCreateCrmApartment = $requestData['code'] === '5.14.1' || $requestData['code'] === '7.13';

        $object27_3 = BObject::where('code', '27.3')->first();
        $objectId = (int) substr($requestData['object_id'], 0, strpos($requestData['object_id'], '::'));
        $objectWorktypeId = substr($requestData['object_id'], strpos($requestData['object_id'], '::') + 2);

        if ($needCreateCrmAvans || $needCreateItr || $needCreateCrmApartment) {
            if ($objectId === $object27_3->id) {
                $organizationId = Organization::where('company_id', 4)->first()?->id ?? Organization::where('company_id', 1)->first()?->id ?? null;
            } else {
                $organizationId = Organization::where('company_id', 1)->first()?->id ?? null;
            }
         } else {
            $organizationId = $requestData['organization_id'];
        }

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
        ]);

        if (! empty($requestData['files'])) {
            foreach ($requestData['files'] as $file) {
                if (! is_null($file)) {
                    $payment->addMedia($file)->toMediaCollection();
                }
            }
        }

        if ($needCreateCrmAvans) {
            $crmAvansDate = isset($requestData['crm_date']) ? get_date_and_month_from_string($requestData['crm_date'], true) : null;
            $crmAvansEmployeeId = $requestData['crm_employee_id'] ?? null;

            $this->createCRMAvans(
                $payment,
                [
                    'employee_id' => $crmAvansEmployeeId,
                    'date' => $crmAvansDate,
                    'crm_not_need_avans' => $crmNotNeedAvans
                ]
            );
        }

        if ($needCreateItr && !$isNotNeedCreateItr) {
            $this->createItr(
                $payment,
                [
                    'id' => $requestData['itr_id'] ?? null,
                    '1c_itr_not_need_create' => $isNotNeedCreateItr
                ]
            );
        }

        if ($needCreateCrmApartment) {
            $crmApartmentId = $requestData['crm_apartment_id'] ?? null;
            $crmApartmentPaymentDate = $requestData['crm_payment_date'] ?? null;
            $crmApartmentPaymentMonth = isset($requestData['crm_payment_month']) ? get_date_and_month_from_string($requestData['crm_payment_month'], true) : null;
            $crmApartmentPaymentAmount = isset($requestData['crm_payment_amount']) ? $this->sanitizer->set($requestData['crm_payment_amount'])->toAmount()->get() : 0;
            $crmApartmentPaymentCommunal = isset($requestData['crm_payment_communal']) ? $this->sanitizer->set($requestData['crm_payment_communal'])->toAmount()->get() : 0;

            $this->createCRMApartment(
                $payment,
                [
                    'crm_apartment_id' => $crmApartmentId,
                    'crm_payment_date' => $crmApartmentPaymentDate,
                    'crm_payment_month' => $crmApartmentPaymentMonth,
                    'crm_payment_amount' => $crmApartmentPaymentAmount,
                    'crm_payment_communal' => $crmApartmentPaymentCommunal
                ]
            );
        }

        return $payment;
    }

    public function updatePayment(CashAccountPayment $payment, array $requestData): void
    {
        $description = $this->sanitizer->set($requestData['description'] ?? '')->upperCaseFirstWord()->get();
        $amount = $this->sanitizer->set($requestData['amount'])->toAmount()->get();
        $isReceiveAmount = isset($requestData['is_receive_amount']);

        if ($payment->type_id === CashAccountPayment::TYPE_OBJECT) {
            $amount = abs($amount);

            if (!$isReceiveAmount) {
                $amount = -$amount;
            }
        }

        $crmNotNeedAvans = isset($requestData['crm_not_need_avans']);

        $isCrmEmployee = $requestData['code'] === '7.8.2' || $requestData['code'] === '7.9.2';

        $isNotNeedCreateItr = isset($requestData['1c_itr_not_need_create']);
        $isItr = ($requestData['code'] === '7.8.1' || $requestData['code'] === '7.9.1' || $requestData['code'] === '7.10');

        $isCrmApartment = $requestData['code'] === '5.14.1' || $requestData['code'] === '7.13';

        $object27_3 = BObject::where('code', '27.3')->first();
        $objectId = (int) substr($requestData['object_id'], 0, strpos($requestData['object_id'], '::'));
        $objectWorktypeId = substr($requestData['object_id'], strpos($requestData['object_id'], '::') + 2);

        if ($isCrmEmployee || $isItr || $isCrmApartment) {
            if ($objectId === $object27_3->id) {
                $organizationId = Organization::where('company_id', 4)->first()?->id ?? Organization::where('company_id', 1)->first()?->id ?? null;
            } else {
                $organizationId = Organization::where('company_id', 1)->first()?->id ?? null;
            }
        } else {
            $organizationId = $requestData['organization_id'];
        }

        $crmAvansData = $payment->getCrmAvansData();
        $itrData = $payment->getItrData();
        $crmApartmentData = $payment->getCrmApartmentData();

        $needCreateCrmAvans = is_null($crmAvansData['id']) && $isCrmEmployee;
        $needUpdateCrmAvans = !is_null($crmAvansData['id']) && $isCrmEmployee;
        $needDeleteCrmAvans = !is_null($crmAvansData['id']) && !$isCrmEmployee;

        $needCreateItr = is_null($itrData['id']) && $isItr && !$isNotNeedCreateItr;
        $needUpdateItr = !is_null($itrData['id']) && $isItr && !$isNotNeedCreateItr;
        $needDeleteItr = !is_null($itrData['id']) && !($isItr && !$isNotNeedCreateItr);

        $needCreateCrmApartment = is_null($crmApartmentData['payment_id']) && $isCrmApartment;
        $needUpdateCrmApartment = !is_null($crmApartmentData['payment_id']) && $isCrmApartment;
        $needDeleteCrmApartment = !is_null($crmApartmentData['payment_id']) && !$isCrmApartment;

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
        ]);

        if (! empty($requestData['files'])) {
            foreach ($requestData['files'] as $file) {
                if (! is_null($file)) {
                    $payment->addMedia($file)->toMediaCollection();
                }
            }
        }

        $crmAvansDate = isset($requestData['crm_date']) ? get_date_and_month_from_string($requestData['crm_date'], true) : null;
        $crmAvansEmployeeId = $requestData['crm_employee_id'] ?? null;

        if ($needCreateCrmAvans) {
            $this->createCRMAvans(
                $payment,
                [
                    'employee_id' => $crmAvansEmployeeId,
                    'date' => $crmAvansDate,
                    'crm_not_need_avans' => $crmNotNeedAvans
                ]
            );
        }

        if ($needUpdateCrmAvans) {
            $this->updateCRMAvans(
                $payment,
                [
                    'employee_id' => $crmAvansEmployeeId,
                    'date' => $crmAvansDate,
                    'crm_not_need_avans' => $crmNotNeedAvans
                ]
            );
        }

        if ($needDeleteCrmAvans) {
            $this->deleteCRMAvans($payment);
        }

        if ($needCreateItr) {
            $this->createItr(
                $payment,
                [
                    'id' => $requestData['itr_id'] ?? null,
                    '1c_itr_not_need_create' => $isNotNeedCreateItr
                ]
            );
        }

        if ($needUpdateItr) {
            $this->updateItr(
                $payment,
                [
                    'id' => $requestData['itr_id'] ?? null,
                    '1c_itr_not_need_create' => $isNotNeedCreateItr
                ]
            );
        }

        if ($needDeleteItr) {
            $this->deleteItr($payment);
        }

        $crmApartmentId = $requestData['crm_apartment_id'] ?? null;
        $crmApartmentPaymentDate = $requestData['crm_payment_date'] ?? null;
        $crmApartmentPaymentMonth = isset($requestData['crm_payment_month']) ? get_date_and_month_from_string($requestData['crm_payment_month'], true) : null;
        $crmApartmentPaymentAmount = isset($requestData['crm_payment_amount']) ? $this->sanitizer->set($requestData['crm_payment_amount'])->toAmount()->get() : 0;
        $crmApartmentPaymentCommunal = isset($requestData['crm_payment_communal']) ? $this->sanitizer->set($requestData['crm_payment_communal'])->toAmount()->get() : 0;

        if ($needCreateCrmApartment) {
            $this->createCRMAvans(
                $payment,
                [
                    'crm_apartment_id' => $crmApartmentId,
                    'crm_payment_date' => $crmApartmentPaymentDate,
                    'crm_payment_month' => $crmApartmentPaymentMonth,
                    'crm_payment_amount' => $crmApartmentPaymentAmount,
                    'crm_payment_communal' => $crmApartmentPaymentCommunal
                ]
            );
        }

        if ($needUpdateCrmApartment) {
            $this->updateCRMApartment(
                $payment,
                [
                    'crm_apartment_id' => $crmApartmentId,
                    'crm_payment_date' => $crmApartmentPaymentDate,
                    'crm_payment_month' => $crmApartmentPaymentMonth,
                    'crm_payment_amount' => $crmApartmentPaymentAmount,
                    'crm_payment_communal' => $crmApartmentPaymentCommunal
                ]
            );
        }

        if ($needDeleteCrmApartment) {
            $this->deleteCRMApartment($payment);
        }
    }

    public function destroyPayment(CashAccountPayment $payment): void
    {
        if (! is_null($payment->getCrmAvansData()['id'])) {
            $this->deleteCRMAvans($payment);
        }

        $payment->delete();
    }

    public function createCRMAvans(CashAccountPayment $payment, array $additionalData): CashAccountPayment
    {
        if ($additionalData['crm_not_need_avans']) {
            $avansId = null;
        } else {
            $avans = new Avans;
            $avans->u_id = auth()->user()->crm_user_id ?? 1;
            $avans->e_id = $additionalData['employee_id'];
            $avans->type = 'Затраты';
            $avans->date = $additionalData['date'];
            $avans->code = $payment->getObjectCode();
            $avans->issue_date = Carbon::now();
            $avans->user_change_id = auth()->user()->crm_user_id ?? 1;
            $avans->updated_at = Carbon::now();
            $avans->value = abs($payment->amount);
            $avans->save();

            $avansId = $avans->id;
        }

        $crmEmployee = Employee::find($additionalData['employee_id']);
        $currentAdditionalData = json_decode($payment->additional_data, true) ?? [];

        $currentAdditionalData['crm_avans'] = [
            'id' => $avansId,
            'employee_id' => $additionalData['employee_id'],
            'employee_uid' => $crmEmployee ? $crmEmployee->getUniqueID() : null,
            'employee_name' => $crmEmployee ? $crmEmployee->getFullname() : null,
            'date' => $additionalData['date'],
            'crm_not_need_avans' => $additionalData['crm_not_need_avans'],
        ];

        $payment->update([
            'additional_data' => json_encode($currentAdditionalData)
        ]);

        return $payment;
    }

    public function updateCRMAvans(CashAccountPayment $payment, array $additionalData): CashAccountPayment
    {
        if ($additionalData['crm_not_need_avans']) {
            $crmEmployee = Employee::find($additionalData['employee_id']);
            $currentAdditionalData = json_decode($payment->additional_data, true) ?? [];

            $currentAdditionalData['crm_avans'] = [
                'id' => null,
                'employee_id' => $additionalData['employee_id'],
                'employee_uid' => $crmEmployee ? $crmEmployee->getUniqueID() : null,
                'employee_name' => $crmEmployee ? $crmEmployee->getFullname() : null,
                'date' => $additionalData['date'],
            ];

            $payment->update([
                'additional_data' => json_encode($currentAdditionalData)
            ]);

            return $payment;
        }

        $crmAvansData = $payment->getCrmAvansData();
        $avans = Avans::find($crmAvansData['id']);

        if (! $avans) {
            return $payment;
        }

        $avans->e_id = $additionalData['employee_id'];
        $avans->date = $additionalData['date'];
        $avans->code = $payment->getObjectCode();
        $avans->user_change_id = auth()->user()->crm_user_id ?? 1;
        $avans->updated_at = Carbon::now();
        $avans->value = abs($payment->amount);
        $avans->update();

        $crmEmployee = Employee::find($additionalData['employee_id']);
        $currentAdditionalData = json_decode($payment->additional_data, true) ?? [];

        $currentAdditionalData['crm_avans'] = [
            'id' => $avans->id,
            'employee_id' => $additionalData['employee_id'],
            'employee_uid' => $crmEmployee ? $crmEmployee->getUniqueID() : null,
            'employee_name' => $crmEmployee ? $crmEmployee->getFullname() : null,
            'date' => $additionalData['date'],
        ];

        $payment->update([
            'additional_data' => json_encode($currentAdditionalData)
        ]);

        return $payment;
    }

    public function deleteCRMAvans(CashAccountPayment $payment): void
    {
        $crmAvansData = $payment->getCrmAvansData();
        $avans = Avans::find($crmAvansData['id']);

        if ($avans) {
            $avans->delete();

            $additionalData = json_decode($payment->additional_data, true) ?? [];
            $additionalData['crm_avans'] = [];

            $payment->update([
                'additional_data' => json_encode($additionalData)
            ]);
        }
    }

    public function createItr(CashAccountPayment $payment, array $additionalData): CashAccountPayment
    {
        $itrList = Cache::get('itr_list_1c_data', []);

        $itrName = null;
        foreach ($itrList as $itr) {
            if ($itr['Id'] === $additionalData['id']) {
                $itrName = $itr['Name'];
                break;
            }
        }

        $currentAdditionalData = json_decode($payment->additional_data, true) ?? [];

        $currentAdditionalData['itr'] = [
            'id' => $additionalData['id'],
            '1c_itr_not_need_create' => $additionalData['1c_itr_not_need_create'],
            'name' => $itrName,
        ];

        $payment->update([
            'additional_data' => json_encode($currentAdditionalData)
        ]);

        return $payment;
    }

    public function updateItr(CashAccountPayment $payment, array $additionalData): CashAccountPayment
    {
        $itrList = Cache::get('itr_list_1c_data', []);

        $itrName = null;
        foreach ($itrList as $itr) {
            if ($itr['Id'] === $additionalData['id']) {
                $itrName = $itr['Name'];
                break;
            }
        }
        $currentAdditionalData = json_decode($payment->additional_data, true) ?? [];

        $currentAdditionalData['itr'] = [
            'id' => $additionalData['id'],
            '1c_itr_not_need_create' => $additionalData['1c_itr_not_need_create'],
            'name' => $itrName,
        ];

        $payment->update([
            'additional_data' => json_encode($currentAdditionalData)
        ]);

        return $payment;
    }

    public function deleteItr(CashAccountPayment $payment): void
    {
        $additionalData = json_decode($payment->additional_data, true) ?? [];
        $additionalData['itr'] = [];

        $payment->update([
            'additional_data' => json_encode($additionalData)
        ]);
    }

    public function createObjectPayment(CashAccountPayment $payment, array $additionalData): CashAccountPayment
    {
        $currentAdditionalData = json_decode($payment->additional_data, true) ?? [];

        $currentAdditionalData['object_payment'] = [
            'object_payment_id' => $additionalData['object_payment_id'],
        ];

        $payment->update([
            'additional_data' => json_encode($currentAdditionalData)
        ]);

        $payment->update([
            'status_id' => CashAccountPayment::STATUS_VALIDATED
        ]);

        return $payment;
    }

    public function createCRMApartment(CashAccountPayment $payment, array $additionalData): CashAccountPayment
    {
        $aPayment = new ApartmentPayment;
        $aPayment->date = $additionalData['crm_payment_date'];
        $aPayment->month = $additionalData['crm_payment_month'];
        $aPayment->sum = $additionalData['crm_payment_amount'];
        $aPayment->communal = $additionalData['crm_payment_communal'];
        $aPayment->a_id = $additionalData['crm_apartment_id'];
        $aPayment->author_id = auth()->user()->crm_user_id ?? 1;
        $aPayment->datetime = Carbon::now();
        $aPayment->save();

        $crmApartment = Apartment::find($additionalData['crm_apartment_id']);
        $currentAdditionalData = json_decode($payment->additional_data, true) ?? [];

        $currentAdditionalData['crm_apartment'] = [
            'payment_id' => $aPayment->id,
            'apartment_id' => $additionalData['crm_apartment_id'],
            'apartment_address' => $crmApartment ? $crmApartment->address : '',
            'payment_date' => $additionalData['crm_payment_date'],
            'payment_month' => $additionalData['crm_payment_month'],
            'payment_amount' => $additionalData['crm_payment_amount'],
            'payment_communal' => $additionalData['crm_payment_communal'],
        ];

        $payment->update([
            'additional_data' => json_encode($currentAdditionalData)
        ]);

        return $payment;
    }

    public function updateCRMApartment(CashAccountPayment $payment, array $additionalData): CashAccountPayment
    {
        $crmApartmentData = $payment->getCrmApartmentData();
        $aPayment = ApartmentPayment::find($crmApartmentData['payment_id']);

        if (! $aPayment) {
            return $payment;
        }

        $aPayment->a_id = $additionalData['crm_apartment_id'];
        $aPayment->date = $additionalData['crm_payment_date'];
        $aPayment->month = $additionalData['crm_payment_month'];
        $aPayment->sum = $additionalData['crm_payment_amount'];
        $aPayment->communal = $additionalData['crm_payment_communal'];
        $aPayment->update();

        $crmApartment = Apartment::find($additionalData['crm_apartment_id']);
        $currentAdditionalData = json_decode($payment->additional_data, true) ?? [];

        $currentAdditionalData['crm_apartment'] = [
            'payment_id' => $aPayment->id,
            'apartment_id' => $additionalData['crm_apartment_id'],
            'apartment_address' => $crmApartment ? $crmApartment->address : '',
            'payment_date' => $additionalData['crm_payment_date'],
            'payment_month' => $additionalData['crm_payment_month'],
            'payment_amount' => $additionalData['crm_payment_amount'],
            'payment_communal' => $additionalData['crm_payment_communal'],
        ];

        $payment->update([
            'additional_data' => json_encode($currentAdditionalData)
        ]);

        return $payment;
    }

    public function deleteCRMApartment(CashAccountPayment $payment): void
    {
        $crmApartmentData = $payment->getCrmApartmentData();
        $aPayment = ApartmentPayment::find($crmApartmentData['payment_id']);

        if ($aPayment) {
            $aPayment->delete();

            $additionalData = json_decode($payment->additional_data, true) ?? [];
            $additionalData['crm_apartment'] = [];

            $payment->update([
                'additional_data' => json_encode($additionalData)
            ]);
        }
    }
}
