<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\Company;
use App\Models\CRM\AvansImport;
use App\Models\Object\BObject;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\PaymentImport;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PaymentService
{
    private Sanitizer $sanitizer;
    private array $opsteList;
    private array $radList;
    private array $materialList;
    private string $error = '';

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function loadCategoriesList(): void
    {
        $this->opsteList = include base_path('resources/categories/opste.php');
        $this->radList = include base_path('resources/categories/rad.php');
        $this->materialList = include base_path('resources/categories/material.php');
    }

    public function filterPayments(array $requestData, $needPaginate = false): Collection|LengthAwarePaginator
    {
        $paymentQuery = Payment::query();

        if (! empty($requestData['period'])) {
            $period = str_replace('/', '.', $requestData['period']);
            $startDate = substr($period, 0, strpos($period, ' '));
            $endDate = substr($period, strpos($period, ' ') + 3);

            $paymentQuery->whereBetween('date', [Carbon::parse($startDate), Carbon::parse($endDate)]);
        }

        if (! empty($requestData['description'])) {
            $paymentQuery->where('description', 'LIKE', '%' . $requestData['description'] . '%');
        }

        if (! empty($requestData['company_id'])) {
            $paymentQuery->whereIn('company_id', $requestData['company_id']);
        }

        if (! empty($requestData['organization_sender_id'])) {
            $paymentQuery->whereIn('organization_sender_id', $requestData['organization_sender_id']);
        }

        if (! empty($requestData['organization_receiver_id'])) {
            $paymentQuery->whereIn('organization_receiver_id', $requestData['organization_receiver_id']);
        }

        if (! empty($requestData['object_id'])) {
            $paymentQuery->whereIn('object_id', $requestData['object_id']);
        }

        if (! empty($requestData['object_worktype_id'])) {
            $paymentQuery->whereIn('object_worktype_id', $requestData['object_worktype_id']);
        }

        if (! empty($requestData['category'])) {
            $paymentQuery->whereIn('category', $requestData['category']);
        }

        if (! empty($requestData['import_type_id'])) {
            $paymentImportsIds = PaymentImport::whereIn('type_id', $requestData['import_type_id'])->pluck('id');
            $paymentQuery->whereIn('import_id', $paymentImportsIds);
        }

        if (! empty($requestData['bank_id'])) {
            $paymentQuery->whereIn('bank_id', $requestData['bank_id']);
        }

        if (! empty($requestData['amount_expression'])) {
            $expression = str_replace(' ', '', $requestData['amount_expression']);
            $expression = str_replace(',', '.', $expression);

            $operators = ['<=', '<', '>=', '>', '!=', '='];
            foreach ($operators as $operator) {
                if (str_contains($expression, $operator)) {
                    $amount = (float) substr($expression, strpos($expression, $operator) + strlen($operator));
                    $paymentQuery->where('amount', $operator, $amount);
                    break;
                }
            }
        }

        $paymentQuery->with('company', 'createdBy', 'object', 'organizationReceiver', 'organizationSender');

        if (! empty($requestData['sort_by'])) {
            if ($requestData['sort_by'] == 'company_id') {
                $paymentQuery->orderBy(Company::select('name')->whereColumn('companies.id', 'payments.company_id'), $requestData['sort_direction'] ?? 'asc');
            } elseif ($requestData['sort_by'] == 'organization_id') {
                $paymentQuery->orderBy(Organization::select('name')->whereColumn('organizations.id', 'payments.organization_receiver_id')->orWhereColumn('organizations.id', 'payments.organization_sender_id'), $requestData['sort_direction'] ?? 'asc');
            } elseif ($requestData['sort_by'] == 'object_id') {
                $paymentQuery->orderBy(BObject::select('code')->whereColumn('objects.id', 'payments.object_id'), $requestData['sort_direction'] ?? 'asc');
            } else {
                $paymentQuery->orderBy($requestData['sort_by'], $requestData['sort_direction'] ?? 'asc');
            }
        } else {
            $paymentQuery->orderByDesc('date')
                ->orderByDesc('id');
        }

        if ($needPaginate) {
            return $paymentQuery->paginate($requestData['count_in_page'] ?? 30)->withQueryString();
        }

        return $paymentQuery->get();
    }

    public function createPayment(array $requestData): Payment
    {
        if (array_key_exists('base_payment_id', $requestData)) {
            $basePayment = Payment::find($requestData['base_payment_id']);
            $requestData = $basePayment->attributesToArray();
        }

        $payment = Payment::create([
            'import_id' => $requestData['import_id'],
            'company_id' => $requestData['company_id'],
            'bank_id' => $requestData['bank_id'],
            'object_id' => $requestData['object_id'],
            'object_worktype_id' => $requestData['object_worktype_id'],
            'organization_sender_id' => $requestData['organization_sender_id'],
            'organization_receiver_id' => $requestData['organization_receiver_id'],
            'type_id' => $requestData['type_id'],
            'payment_type_id' => $requestData['payment_type_id'],
            'category' => $requestData['category'],
            'code' => $this->sanitizer->set($requestData['code'])->toCode()->get(),
            'description' => $this->sanitizer->set($requestData['description'])->upperCaseFirstWord()->get(),
            'date' => $requestData['date'],
            'amount' => $requestData['amount'],
            'amount_without_nds' => $requestData['amount_without_nds'],
            'is_need_split' => $requestData['is_need_split'],
            'status_id' => $requestData['status_id']
        ]);

        return $payment;
    }

    public function updatePayment(Payment $payment, array $requestData): Payment
    {
        if (array_key_exists('amount', $requestData)) {
            $description = array_key_exists('description', $requestData) ? $requestData['description'] : $payment->description;
            $requestData['amount'] = $this->sanitizer->set($requestData['amount'])->toAmount()->get();
            $nds = $this->checkHasNDSFromDescription($description) ? round($requestData['amount'] / 6, 2) : 0;
            $requestData['amount_without_nds'] = $requestData['amount'] - $nds;
        } elseif (array_key_exists('object_code', $requestData)) {
            $requestData['object_id'] = null;
            $requestData['object_worktype_id'] = null;
            $requestData['type_id'] = Payment::TYPE_NONE;

            if ($requestData['object_code'] === 'Трансфер') {
                $requestData['type_id'] = Payment::TYPE_TRANSFER;
            } else if ($requestData['object_code'] === 'Общее') {
                $requestData['type_id'] = Payment::TYPE_GENERAL;
            } else if (! empty($requestData['object_code'])) {
                $code = substr($requestData['object_code'], 0, strpos($requestData['object_code'], '.'));
                $workType = substr($requestData['object_code'], strpos($requestData['object_code'], '.') + 1);
                $object = BObject::where('code', $code)->first();

                if ($object) {
                    $requestData['type_id'] = Payment::TYPE_OBJECT;
                    $requestData['object_id'] = $object->id;
                    $requestData['object_worktype_id'] = (int) $workType;
                } else {
                    $this->error = 'Объект ' . $requestData['object_code'] . ' не найден в системе. Данные об объекте не сохранятся.';
                }
            }

            unset($requestData['object_code']);
        } elseif (array_key_exists('code', $requestData)) {
            $requestData['code'] = $this->sanitizer->set($requestData['code'])->toCode()->get();
        } elseif (array_key_exists('description', $requestData)) {
            $isNeedSplit = $this->checkIsNeedSplitFromDescription($requestData['description']);
            $requestData['is_need_split'] = $isNeedSplit;
        }

        $payment->update($requestData);

        $isCode = false;
        if (empty($payment->code) && $payment->type_id !== Payment::TYPE_OBJECT) {
            $isCode = true;
        } elseif (! empty($payment->code)) {
            $isCode = true;
        }

        if (
            $payment->type_id !== Payment::TYPE_NONE
            && $isCode
            && ! empty($payment->description)
            && ! is_null($payment->category)
            && ! is_null($payment->amount)
        ) {
            if (! $payment->isActive()) {
                $payment->setActive();
            }
        } else {
            if (! $payment->isBlocked()) {
                $payment->setBlocked();
            }
        }

        return $payment;
    }

    public function destroyPayment(Payment $payment): Payment
    {
        $payment->delete();

        return $payment;
    }

    public function splitPayment(Payment $payment, array $request): Collection
    {
        $import = $payment->import;
        $avansImport = AvansImport::find($request['crm_avans_import_id']);
        $avansImport->load('items', 'items.avans');

        $amountGroupedByObjectCode = [];
        foreach ($avansImport->items as $item) {
            if (! isset($amountGroupedByObjectCode[$item->avans->code])) {
                $amountGroupedByObjectCode[$item->avans->code] = 0;
            }
            $amountGroupedByObjectCode[$item->avans->code] += $item->avans->value;
        }

        $description = $this->sanitizer->set($avansImport->description)->lowerCase()->get();
        $costCode = str_contains($description, 'зарплат') ? '7.17' : '7.26';

        $company = Company::find(1);
        $organizationSenderId = $company->organizations()->first()->id;
        $organizationReceiverId = Organization::where('name', 'ФИЛИАЛ № 7701 БАНКА ВТБ (ПАО) Г. МОСКВА')->first()->id;

        $payments = [];
        foreach ($amountGroupedByObjectCode as $code => $amount) {
            $objectCode = substr($code, 0, strpos($code, '.'));
            $worktypeCode = (int) substr($code, strpos($code, '.') + 1);
            $payments[] = $this->createPayment([
                'company_id' => $company->id,
                'bank_id' => 1,
                'import_id' => $payment->import_id,
                'object_id' => BObject::where('code', $objectCode)->first()->id ?? null,
                'object_worktype_id' => $worktypeCode,
                'organization_sender_id' => $organizationSenderId,
                'organization_receiver_id' => $organizationReceiverId,
                'type_id' => Payment::TYPE_OBJECT,
                'payment_type_id' => Payment::PAYMENT_TYPE_NON_CASH,
                'code' => $costCode,
                'category' => Payment::CATEGORY_RAD,
                'description' => $payment->description,
                'date' => $import->date,
                'amount' => (float) -$amount,
                'amount_without_nds' => (float) -$amount,
                'is_need_split' => false,
                'status_id' => Status::STATUS_ACTIVE
            ]);
        }

        $this->destroyPayment($payment);
        $import->reCalculateAmountsAndCounts();

        return collect($payments)->collect();
    }

    public function checkHasNDSFromDescription(string $description): bool
    {
        $description = $this->sanitizer->set($description)->noSpaces()->lowerCase()->get();

        if (
            ! str_contains($description, 'вт.ч.ндс')
            && ! str_contains($description, 'втомчислендс')
        ) {
            return false;
        }

        return true;
    }

    public function checkIsNeedSplitFromDescription(string $description): bool
    {
        $description = $this->sanitizer->set($description)->lowerCase()->get();

        if (
            str_contains($description, 'перечисление заработной платы')
            || str_contains($description, 'перечисление отпускных')
            || str_contains($description, 'оплата больничного')
        ) {
            return true;
        }

        return false;
    }

    public function findCategoryFromDescription(string $description): null|string
    {
        $description = $this->sanitizer->set($description)->lowerCase()->get();

        foreach ($this->opsteList as $opsteValue) {
            if (str_contains($description, $opsteValue)) {
                return Payment::CATEGORY_OPSTE;
            }
        }

        foreach ($this->radList as $radValue) {
            if (str_contains($description, $radValue)) {
                return Payment::CATEGORY_RAD;
            }
        }

        foreach ($this->materialList as $materialValue) {
            if (str_contains($description, $materialValue)) {
                return Payment::CATEGORY_MATERIAL;
            }
        }

        return null;
    }

    public function hasError()
    {
        return ! empty($this->error);
    }

    public function getError()
    {
        return $this->error;
    }
}
