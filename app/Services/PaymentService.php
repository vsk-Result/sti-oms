<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\Company;
use App\Models\CRM\AvansImport;
use App\Models\Loan;
use App\Models\LoanNotifyTag;
use App\Models\Object\BObject;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\PaymentImport;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class PaymentService
{
    private OrganizationService $organizationService;
    private Sanitizer $sanitizer;
    private CurrencyExchangeRateService $rateService;
    private array $opsteList;
    private array $radList;
    private array $materialList;
    private array $salaryList;
    private array $taxList;
    private string $error = '';

    public function __construct(
        Sanitizer $sanitizer, OrganizationService $organizationService,
        CurrencyExchangeRateService $rateService
    )
    {
        $this->sanitizer = $sanitizer;
        $this->organizationService = $organizationService;
        $this->rateService = $rateService;
    }

    public function loadCategoriesList(): void
    {
        $this->opsteList = include base_path('resources/categories/opste.php');
        $this->radList = include base_path('resources/categories/rad.php');
        $this->materialList = include base_path('resources/categories/material.php');
        $this->salaryList = include base_path('resources/categories/salary.php');
        $this->taxList = include base_path('resources/categories/tax.php');
    }

    public function filterPayments(array $requestData, bool $needPaginate = false, array &$totalInfo = []): Builder|LengthAwarePaginator
    {
        $paymentQuery = Payment::query();

        if (! empty($requestData['period'])) {
            $period = explode(' - ', $requestData['period']);
            $paymentQuery->whereBetween('date', [Carbon::parse($period[0]), Carbon::parse($period[1])]);
        }

        if (! empty($requestData['year']) && ! empty($requestData['month'])) {
            $paymentQuery->where('date', 'LIKE', $requestData['year'] . '-' . $requestData['month'] . '%');
        }

        if (! empty($requestData['description'])) {
            $paymentQuery->where('description', 'LIKE', '%' . $requestData['description'] . '%');
        }

        if (! empty($requestData['company_id'])) {
            $paymentQuery->whereIn('company_id', $requestData['company_id']);
        }

        if (! empty($requestData['currency'])) {
            $paymentQuery->whereIn('currency', $requestData['currency']);
        }

        if (! empty($requestData['organization_id'])) {
            $organizationIds = $requestData['organization_id'];
            $paymentQuery->where(function($q) use ($organizationIds) {
                $q->whereIn('organization_sender_id', $organizationIds)->orWhereIn('organization_receiver_id', $organizationIds);
            });
        }
        if (! empty($requestData['object_id'])) {

            $hasGeneral = in_array('Общее', $requestData['object_id']);
            $hasTransfer = in_array('Трансфер', $requestData['object_id']);

            if ($hasGeneral) {
                unset($requestData['object_id'][array_search('Общее', $requestData['object_id'])]);
            }

            if ($hasTransfer) {
                unset($requestData['object_id'][array_search('Трансфер', $requestData['object_id'])]);
            }

            if (! empty($requestData['object_id'])) {
                if ($hasGeneral || $hasTransfer) {
                    $requestData['object_id'][] = null;
                }
                $paymentQuery->whereIn('object_id', $requestData['object_id']);
            }

            if ($hasGeneral || $hasTransfer) {
                unset($requestData['object_id'][array_search(null, $requestData['object_id'])]);
                $paymentQuery->where(function($q) use($hasGeneral, $hasTransfer, $requestData) {

                    if (! empty($requestData['object_id'])) {
                        $q->orWhere('type_id', Payment::TYPE_OBJECT);
                    }

                    if ($hasGeneral) {
                        $q->orWhere('type_id', Payment::TYPE_GENERAL);
                    }

                    if ($hasTransfer) {
                        $q->orWhere('type_id', Payment::TYPE_TRANSFER);
                    }
                });
            }
        } else {
            if (auth()->user()->hasRole(['object-leader', 'finance-object-user'])) {
                $paymentQuery->whereIn('object_id', auth()->user()->objects->pluck('id'));
            }
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

        if (! empty($requestData['amount_expression_operator']) && isset($requestData['amount_expression'])) {
            $expressionAmount = str_replace(',', '.', $requestData['amount_expression']);
            $expressionAmount = preg_replace("/[^-.0-9]/", '', $expressionAmount);

            $paymentQuery->where('amount', $requestData['amount_expression_operator'], $expressionAmount);
        }

        if (! empty($requestData['payment_type_id'])) {
            $paymentQuery->whereIn('payment_type_id', $requestData['payment_type_id']);
        }

        if (! empty($requestData['code'])) {
            $paymentQuery->whereIn('code', $requestData['code']);
        }

        if (! empty($requestData['parameter_font_color'])) {
            $paymentQuery->where(function ($q) use ($requestData) {
                foreach ($requestData['parameter_font_color'] as $parameter) {
                    [$key, $value] = explode('::', $parameter);
                    $q->orWhere('parameters', 'LIKE', '%"' . $key . '": "' . $value . '"%');
                }
            });
        }

        if (! empty($requestData['parameter_background_color'])) {
            $paymentQuery->where(function ($q) use ($requestData) {
                foreach ($requestData['parameter_background_color'] as $parameter) {
                    [$key, $value] = explode('::', $parameter);
                    $q->orWhere('parameters', 'LIKE', '%"' . $key . '": "' . $value . '"%');
                }
            });
        }

        if (! empty($requestData['sort_by'])) {
            if ($requestData['sort_by'] == 'company_id') {
                $paymentQuery->orderBy(Company::select('name')->whereColumn('companies.id', 'payments.company_id'), $requestData['sort_direction'] ?? 'asc');
            } elseif ($requestData['sort_by'] == 'organization_id') {
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

        $paymentQuery->with('company', 'import', 'createdBy', 'object', 'audits', 'organizationSender', 'organizationReceiver');

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

    public function createPayment(array $requestData): Payment
    {
        if (array_key_exists('base_payment_id', $requestData)) {
            $basePayment = Payment::find($requestData['base_payment_id']);
            $requestData = $basePayment->attributesToArray();
        }

        $paymentCurrency = $requestData['currency'] ?? 'RUB';
        $paymentCurrencyRate = $paymentCurrency !== 'RUB'
                                ? $this->rateService->getExchangeRate($requestData['date'], $paymentCurrency)->rate ?? 0
                                : 1;

        $payment = Payment::create([
            'import_id' => $requestData['import_id'] ?? null,
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
            'description' => $this->sanitizer->set($requestData['description'] ?? '')->upperCaseFirstWord()->get(),
            'date' => $requestData['date'],
            'amount' => $paymentCurrencyRate * $requestData['amount'],
            'parameters' => $requestData['parameters'] ?? [],
            'amount_without_nds' => $paymentCurrency === 'RUB' ? $requestData['amount_without_nds'] : ($paymentCurrencyRate * $requestData['amount']),
            'is_need_split' => $requestData['is_need_split'] ?? false,
            'was_split' => $requestData['was_split'] ?? false,
            'status_id' => $requestData['status_id'] ?? Status::STATUS_ACTIVE,
            'currency' => $paymentCurrency,
            'currency_rate' => $paymentCurrencyRate,
            'currency_amount' => $requestData['amount'],
        ]);

        // Если в описании встретились теги займов/кредитов, отправим уведомление Алле (пока-что)
        $activeLoansIds = Loan::all()->pluck('id')->toArray();
        $tags = LoanNotifyTag::whereIn('loan_id', $activeLoansIds)->get();
        if (count($tags) > 0) {
            foreach ($tags as $tag) {
                if (str_contains($payment->description, $tag->tag)) {
                    try {
                        Mail::send('emails.loans.notify', compact('payment', 'tag'), function ($m) {
                            $m->from('support@st-ing.com', 'OMS Support');
                            $m->to('result007@yandex.ru')
                                ->subject('OMS. Оплата содержит тег займов/кредитов');
                        });
                    } catch(\Exception $e){}
                }
            }
        }

        return $payment;
    }

    public function copyPayment(Payment $payment): void
    {
        $this->createPayment($payment->attributesToArray());
    }

    public function updatePayment(Payment $payment, array $requestData): Payment
    {
        $this->prepareRequestData($requestData, $payment);

        $payment->update($requestData);

        $isCode = false;
        if (empty($payment->code) && $payment->type_id !== Payment::TYPE_OBJECT) {
            $isCode = true;
        } elseif (! empty($payment->code)) {
            $isCode = true;
        }

        $isCategory = false;
        if (empty($payment->category) && $payment->type_id !== Payment::TYPE_OBJECT) {
            $isCategory = true;
        } elseif (! empty($payment->category)) {
            $isCategory = true;
        }

        if (
            $payment->type_id !== Payment::TYPE_NONE
            && $isCode
            && ! empty($payment->description)
            && $isCategory
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

        // Если в описании встретились теги займов/кредитов, отправим уведомление Алле (пока-что)
        $activeLoansIds = Loan::all()->pluck('id')->toArray();
        $tags = LoanNotifyTag::whereIn('loan_id', $activeLoansIds)->get();
        if (count($tags) > 0) {
            foreach ($tags as $tag) {
                if (str_contains($payment->description, $tag->tag)) {
                    try {
                        Mail::send('emails.loans.notify', compact('payment', 'tag'), function ($m) {
                            $m->from('support@st-ing.com', 'OMS Support');
                            $m->to('result007@yandex.ru')
                                ->subject('OMS. Оплата содержит тег займов/кредитов');
                        });
                    } catch(\Exception $e){}
                }
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

        $costCode = $avansImport->type === 'Зарплата' ? '7.17' : '7.26';

        $company = Company::find(1);
        $organizationSenderId = $company->organizations()->first()->id;
        $organizationReceiverId = $this->organizationService->getOrCreateOrganization([
            'inn' => '7702070139',
            'name' => 'ФИЛИАЛ "ЦЕНТРАЛЬНЫЙ" БАНКА ВТБ (ПАО)',
            'company_id' => null,
            'kpp' => null
        ])->id;

        $payments = [];
        $codesWithoutWorktype = BObject::getCodesWithoutWorktype();
        foreach ($amountGroupedByObjectCode as $oCode => $amount) {

            $worktypeCode = null;

            if (isset($codesWithoutWorktype[$oCode])) {
                $code = $codesWithoutWorktype[$oCode];
            } else {
                $code = $oCode;
                $code = substr($code, 0, strpos($code, '.'));

                if (str_contains($oCode, '.')) {
                    $worktypeCode = (int) substr($oCode, strpos($oCode, '.') + 1);
                }
            }

            $payments[] = $this->createPayment([
                'company_id' => $company->id,
                'bank_id' => 1,
                'import_id' => $payment->import_id,
                'object_id' => BObject::where('code', $code)->first()->id ?? null,
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
                'was_split' => true,
                'status_id' => Status::STATUS_ACTIVE
            ]);
        }

        $this->destroyPayment($payment);
        $import->reCalculateAmountsAndCounts();

        $avansImport->is_split = true;
        $avansImport->update();

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
            || str_contains($description, 'перечисление согласно реестру')
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

        foreach ($this->salaryList as $salaryValue) {
            if (str_contains($description, $salaryValue)) {
                return Payment::CATEGORY_SALARY;
            }
        }

        foreach ($this->taxList as $taxValue) {
            if (str_contains($description, $taxValue)) {
                return Payment::CATEGORY_TAX;
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

    public function prepareRequestData(array &$requestData, Payment|null $payment): void
    {
        if (array_key_exists('_token', $requestData)) {
            unset($requestData['_token']);
        }

        if (array_key_exists('return_url', $requestData)) {
            unset($requestData['return_url']);
        }

        if (array_key_exists('amount', $requestData)) {
            $description = array_key_exists('description', $requestData) ? $requestData['description'] : $payment->description ?? '';
            $requestData['amount'] = $this->sanitizer->set($requestData['amount'])->toAmount()->get();
            $organizationID = $requestData['organization_id'] ?? null;
            if ($payment) {
                $organizationID = $payment->amount >= 0 ? $payment->organization_sender_id : $payment->organization_receiver_id;
            }
            $nds = $this->checkNeedNDS($description, $organizationID) ? round($requestData['amount'] / 6, 2) : 0;
            $requestData['amount_without_nds'] = $requestData['amount'] - $nds;
        }

        if (array_key_exists('object_id', $requestData)) {
            $objectId = $requestData['object_id'];
            $requestData['object_id'] = null;
            $requestData['object_worktype_id'] = null;
            $requestData['type_id'] = Payment::TYPE_NONE;

            if (str_contains($objectId, '::')) {
                $oId = (int) substr($objectId, 0, strpos($objectId, '::'));
                $object = BObject::find($oId);
                if ($object) {
                    $requestData['object_id'] = $object->id;
                    $wt = substr($objectId, strpos($objectId, '::') + 2);
                    $requestData['object_worktype_id'] = empty($wt) ? null : $wt;
                    $requestData['type_id'] = Payment::TYPE_OBJECT;
                }
            } else {
                if ((int) $objectId === Payment::TYPE_GENERAL) {
                    $requestData['type_id'] = Payment::TYPE_GENERAL;
                } if ((int) $objectId === Payment::TYPE_TRANSFER) {
                    $requestData['type_id'] = Payment::TYPE_TRANSFER;
                }
            }
        }

        if (array_key_exists('object_code', $requestData)) {
            $requestData['object_id'] = null;
            $requestData['object_worktype_id'] = null;
            $requestData['type_id'] = Payment::TYPE_NONE;

            $requestData['object_code'] = str_replace(',', '.', $requestData['object_code']);

            if ($requestData['object_code'] === 'Трансфер') {
                $requestData['type_id'] = Payment::TYPE_TRANSFER;
            } else if ($requestData['object_code'] === 'Общее') {
                $requestData['type_id'] = Payment::TYPE_GENERAL;
            } else if (! empty($requestData['object_code'])) {
                $codesWithoutWorktype = BObject::getCodesWithoutWorktype();
                if (isset($codesWithoutWorktype[$requestData['object_code']])) {
                    $code = $codesWithoutWorktype[$requestData['object_code']];
                } else {
                    $code = $requestData['object_code'];

                    if (str_contains($requestData['object_code'], '.')) {
                        $code = substr($code, 0, strpos($code, '.'));
                        $requestData['object_worktype_id'] = (int) substr($requestData['object_code'], strpos($requestData['object_code'], '.') + 1);
                    }
                }
                $object = BObject::where('code', $code)->first();

                if ($object) {
                    $requestData['type_id'] = Payment::TYPE_OBJECT;
                    $requestData['object_id'] = $object->id;
                } else {
                    $this->error = 'Объект ' . $requestData['object_code'] . ' не найден в системе. Данные об объекте не сохранятся.';
                }
            }

            unset($requestData['object_code']);
        }

        if (array_key_exists('code', $requestData)) {
            $requestData['code'] = $this->sanitizer->set($requestData['code'])->toCode()->get();
        }

        if (array_key_exists('description', $requestData)) {
            $requestData['description'] = $requestData['description'] ?? '';
            $isNeedSplit = $this->checkIsNeedSplitFromDescription($requestData['description']);
            $requestData['is_need_split'] = $isNeedSplit;

            if (! isset($requestData['amount'])) {
                $requestData['amount'] = $payment->amount;
            }

            $organizationID = $requestData['organization_id'] ?? null;
            if ($payment) {
                $organizationID = $payment->amount >= 0 ? $payment->organization_sender_id : $payment->organization_receiver_id;
            }
            $nds = $this->checkNeedNDS($requestData['description'], $organizationID) ? round($requestData['amount'] / 6, 2) : 0;
            $requestData['amount_without_nds'] = $requestData['amount'] - $nds;
        }

        if (array_key_exists('organization_id', $requestData)) {
            $requestData['organization_sender_id'] = null;
            $requestData['organization_receiver_id'] = null;

            $companyOrganization = $this->organizationService->getOrCreateOrganization([
                'company_id' => 1,
                'name' => 'ООО "Строй Техно Инженеринг"',
                'inn' => '7720734368',
                'kpp' => null
            ]);

            if ($requestData['amount'] < 0) {
                $requestData['organization_sender_id'] = $companyOrganization->id;
                $requestData['organization_receiver_id'] = $requestData['organization_id'];
            } else {
                $requestData['organization_sender_id'] = $requestData['organization_id'];
                $requestData['organization_receiver_id'] = $companyOrganization->id;
            }

            unset($requestData['organization_id']);
        }

        if (array_key_exists('parameters', $requestData)) {
            if (str_contains($requestData['parameters'], '::')) {
                [$key, $value] = explode('::', $requestData['parameters']);

                $parameters = $payment->parameters ?? [];
                $parameters[$key] = $value;
                $requestData['parameters'] = $parameters;
            }
        }

        if ($payment) {
            $paymentCurrency = $requestData['currency'] ?? $payment->currency;
            $paymentCurrencyRate = $paymentCurrency !== 'RUB'
                ? $this->rateService->getExchangeRate($requestData['date'] ?? $payment->date, $paymentCurrency)->rate ?? 0
                : 1;

            $requestData['currency'] = $paymentCurrency;
            $requestData['currency_rate'] = $paymentCurrencyRate;

            if ($paymentCurrency === 'RUB') {
                $requestData['amount'] = $requestData['amount'] ?? $payment->amount;
                $requestData['amount_without_nds'] = $requestData['amount_without_nds'] ?? $payment->amount_without_nds;
                $requestData['currency_amount'] = $requestData['amount'];
            } else {
                $requestData['currency_amount'] = $requestData['amount'] ?? $payment->currency_amount;
                $requestData['amount'] = $requestData['currency_amount'] * $paymentCurrencyRate;
                $requestData['amount_without_nds'] = $requestData['amount'];
            }
        }
    }

    public function checkNeedNDS(string|null $description, int|null $organizationID): bool
    {
        if (is_null($description)) {
            return false;
        }

        if (is_null($organizationID)) {
            return $this->checkHasNDSFromDescription($description);
        }

        $organization = Organization::find($organizationID);

        return $organization->isNDSAuto()
            ? $this->checkHasNDSFromDescription($description)
            : $organization->isNDSAlways();
    }

    public function clearPayments(): void
    {
        $PTICompany = Company::where('name', 'ООО "ПРОМТЕХИНЖИНИРИНГ"')->first();

        if (! $PTICompany) {
            return;
        }

        $imports = PaymentImport::where('company_id', $PTICompany->id)
            ->where('date', '<', Carbon::now()->subWeek()->format('Y-m-d'))
            ->where('status_id', Status::STATUS_BLOCKED)
            ->where('type_id', PaymentImport::TYPE_STATEMENT)
            ->orderByDesc('date')
            ->get();

        foreach ($imports as $import) {
            $import->payments()->whereNull('object_id')->delete();
            $import->reCalculateAmountsAndCounts();

            if ($import->payments_count === 0) {
                $import->delete();
            }
        }
    }

    public function determiningNDSPayments(): void
    {
        $excludePayments = [];
        Payment::chunk(1000, function($payments) use(&$excludePayments) {
            foreach ($payments as $payment) {
                if (in_array($payment->id, $excludePayments)) {
                    continue;
                }
                if ($this->checkHasNDSFromDescription($payment->description)) {
                    if ($payment->amount < 0) {
                        $pts = Payment::where('organization_receiver_id', $payment->organization_receiver_id)->get();
                    } else {
                        $pts = Payment::where('organization_sender_id', $payment->organization_sender_id)->get();
                    }

                    foreach ($pts as $p) {
                        if ($p->amount !== $p->amount_without_nds) {
                            continue;
                        }
                        $nds = round($p->amount / 6, 2);
                        $p->amount_without_nds = $p->amount - $nds;
                        $p->update();
                    }

                    $excludePayments = array_merge($excludePayments, $pts->pluck('id')->toArray());
                }
            }
        });
    }
}
