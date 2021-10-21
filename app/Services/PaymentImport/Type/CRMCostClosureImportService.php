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
    private PaymentService $paymentService;
    private ObjectService $objectService;
    private OrganizationService $organizationService;

    public function __construct(
        PaymentService $paymentService,
        ObjectService $objectService,
        OrganizationService $organizationService
    ) {
        $this->paymentService = $paymentService;
        $this->objectService = $objectService;
        $this->organizationService = $organizationService;
    }

    public function getClosures(): Collection
    {
        return CostClosure::where('is_confirm', true)->where('is_split', false)->with('user')->get();
    }

    public function createImport(array $requestData): PaymentImport
    {
        $closure = CostClosure::find($requestData['crm_cost_closure_id'])->load('cost', 'cost.items', 'user');
        $month = Carbon::parse($closure->date)->format('Y-m');

        $import = PaymentImport::create([
            'type_id' => PaymentImport::TYPE_CRM_COST_CLOSURE,
            'bank_id' => null,
            'company_id' => 1,
            'date' => $closure->date,
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

        foreach ($items as $item) {

            if ($item->object) {
                $objectCode = substr($item->object->code, 0, strpos($item->object->code, '.'));
                if (! $object = BObject::where('code', $objectCode)->first()) {
                    $object = $this->objectService->createObject([
                        'code' => $objectCode,
                        'name' => 'Без названия',
                        'address' => '',
                        'photo' => null
                    ]);
                }

                $objectId = $object->id;
                $worktypeCode = (int) substr($item->object->code, strpos($item->object->code, '.') + 1);
                $typeId = Payment::TYPE_OBJECT;


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
                    'category' => Payment::CATEGORY_RAD,
                    'description' => $item->avans ? ($item->information . ' - ' . $item->avans->employee->getFullName()) : $item->information,
                    'date' => $import->date,
                    'amount' => (float) $item->sum,
                    'amount_without_nds' => (float) $item->sum,
                    'is_need_split' => false,
                    'status_id' => Status::STATUS_ACTIVE
                ]);
            }
        }

        $import->reCalculateAmountsAndCounts();

        return $import;
    }
}
