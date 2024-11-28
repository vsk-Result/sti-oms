<?php

namespace App\Services\PaymentImport\Type;

use App\Models\CRM\CostClosure;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Models\PaymentImport;
use App\Models\Status;
use App\Services\ObjectService;
use App\Services\OrganizationService;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CRMCostClosureImportService
{
    private string $error;
    private PaymentService $paymentService;
    private ObjectService $objectService;
    private OrganizationService $organizationService;

    public function __construct(
        PaymentService $paymentService,
        ObjectService $objectService,
        OrganizationService $organizationService
    ) {
        $this->error = '';
        $this->paymentService = $paymentService;
        $this->objectService = $objectService;
        $this->organizationService = $organizationService;
    }

    public function getClosures(): Collection
    {
        return CostClosure::where('is_confirm', false)->where('is_split', false)->with('user')->get();
    }

    public function createImport(array $requestData): PaymentImport
    {
        $closure = CostClosure::find($requestData['crm_cost_closure_id'])->load('cost', 'cost.items', 'user');
        $month = Carbon::parse($closure->date)->format('Y-m');

        $import = PaymentImport::create([
            'type_id' => PaymentImport::TYPE_CRM_COST_CLOSURE,
            'bank_id' => null,
            'company_id' => 1,
            'date' => now()->format('Y-m-d'),
            'status_id' => Status::STATUS_ACTIVE,
            'file' => null,
            'description' => 'Закрытый период кассы ' . $closure->user->name . ' за ' . Carbon::parse($closure->date)->format('F Y')
        ]);

        $items = $closure->cost->items()
            ->where('date', 'LIKE', $month . '%')
            ->where('is_close', true)
            ->with('object', 'avans')
            ->orderBy('date', 'ASC')
            ->get();

        $companyOrganization = $this->organizationService->getOrCreateOrganization([
            'company_id' => $import->company->id,
            'name' => $import->company->name,
            'inn' => $import->company->inn,
            'kpp' => null
        ]);

        $codesWithoutWorktype = BObject::getCodesWithoutWorktype();

        foreach ($items as $item) {

            if ($item->object || $item->type_id === 1 || $item->type_id === 2) {

                $objectId = null;
                $worktypeCode = null;

                if ($item->type_id === 1 && ! $item->object) {
                    $typeId = Payment::TYPE_TRANSFER;
                } else if ($item->type_id === 2 && ! $item->object) {
                    $typeId = Payment::TYPE_TRANSFER;
                } else {
                    if (isset($codesWithoutWorktype[$item->object->code])) {
                        $objectCode = $codesWithoutWorktype[$item->object->code];
                    } else {
                        $objectCode = $item->object->code;

                        if (str_contains($item->object->code, '.')) {
                            $objectCode = substr($objectCode, 0, strpos($objectCode, '.'));
                            $worktypeCode = (int) substr($item->object->code, strpos($item->object->code, '.') + 1);
                        }
                    }

                    if (! $object = BObject::where('code', $objectCode)->first()) {
                        $this->error = 'Объект "' . $objectCode . '" не найден в системе. Загрузка не удалась.';
                        return $import;
                    }

                    $objectId = $object->id;
                    $typeId = Payment::TYPE_OBJECT;
                }

                if ((float) $item->sum < 0) {
                    $organizationSender = $companyOrganization;
                    $organizationReceiver = $this->organizationService->getOrCreateOrganization([
                        'inn' => null,
                        'name' => $item->client,
                        'company_id' => null,
                        'kpp' => null
                    ]);
                } else {
                    $organizationSender = $this->organizationService->getOrCreateOrganization([
                        'inn' => null,
                        'name' => $item->client,
                        'company_id' => null,
                        'kpp' => null
                    ]);
                    $organizationReceiver = $companyOrganization;
                }

                $category = $item->category;
                if (empty($category)) {
                    $category = $this->getCategoryFromKostCode($item->getKostCode(), $item->information);
                }

                $this->paymentService->createPayment([
                    'company_id' => $import->company_id,
                    'bank_id' => $import->bank_id,
                    'import_id' => $import->id,
                    'object_id' => $objectId,
                    'object_worktype_id' => $worktypeCode,
                    'organization_sender_id' => $organizationSender->id,
                    'organization_receiver_id' => $organizationReceiver->id,
                    'type_id' => $typeId,
                    'payment_type_id' => Payment::PAYMENT_TYPE_CASH,
                    'code' => $item->getKostCode(),
                    'category' => $category,
                    'description' => $item->avans ? ($item->information . ' - ' . $item->avans->employee->getFullName()) : $item->information,
                    'date' => $item->date,
                    'amount' => (float) $item->sum,
                    'amount_without_nds' => (float) $item->sum,
                    'is_need_split' => false,
                    'was_split' => true,
                    'status_id' => Status::STATUS_ACTIVE
                ]);
            }
        }

        $import->reCalculateAmountsAndCounts();

        $closure->is_confirm = true;
        $closure->is_split = true;
        $closure->update();

        return $import;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function hasError(): bool
    {
        return ! empty($this->error);
    }

    public function getCategoryFromKostCode($code, $description)
    {
        $salary = ['7.17', '7.18', '7.26', '7.26.1', '7.26.2', '7.17.1', '7.17.2'];
        $provider = [
            '1.1.6', '1.1.7', '1.2.4', '2.1.1', '2.1.10', '2.1.2', '2.1.7', '2.2', '2.2.4', '3', '3.1.2',
            '3.2.2', '4.2', '5.10', '5.2', '7.10', '7.29', '7.32', '7.33', '7.6', '7.7'];
        $transfer = ['7.15'];
        $service = ['5.5', '7.11', '7.21', '7.22', '7.23', '7.24', '7.27', '7.30', '7.31', '7.5', '7.8', '7.7',];

        if (in_array($code, $salary)) {
            return Payment::CATEGORY_SALARY;
        }
        if (in_array($code, $provider)) {
            return Payment::CATEGORY_MATERIAL;
        }
        if (in_array($code, $transfer)) {
            return Payment::CATEGORY_TRANSFER;
        }
        if (in_array($code, $service)) {
            return Payment::CATEGORY_OPSTE;
        }

        if (mb_strpos(mb_strtolower($description), 'получение средств')) {
            return Payment::CATEGORY_TRANSFER;
        }

        return Payment::CATEGORY_RAD;
    }
}
