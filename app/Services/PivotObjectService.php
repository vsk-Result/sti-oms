<?php

use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\Object\BObject;
use App\Services\Contract\ContractService;
use App\Services\CurrencyExchangeRateService;
use App\Services\PivotObjectDebtService;

/**
 * Сервис для предоставления сводной информации об объекте
 */
class PivotObjectService
{
    /**
     * @var PivotObjectDebtService
     * Сервис для предоставления информации о долгах постащикам, подрядчикам и за услуги
     */
    private PivotObjectDebtService $pivotObjectDebtService;

    /**
     * @var CurrencyExchangeRateService
     * Сервис для предоставления информации о курсах валют
     */
    private CurrencyExchangeRateService $currencyService;

    /**
     * @var ContractService
     * Сервис для предоставления информации о договорах и актах
     */
    private ContractService $contractService;

    private array $availableCodes = ['346', '349', '353', '358', '359', '360', '361', '363'];

    public function __construct(
        PivotObjectDebtService $pivotObjectDebtService,
        CurrencyExchangeRateService $currencyService,
        ContractService $contractService,
    ) {
        $this->pivotObjectDebtService = $pivotObjectDebtService;
        $this->currencyService = $currencyService;
        $this->contractService = $contractService;
    }

    public function getPivotInfoByObjectId(int $objectId): array
    {
        $object = BObject::find($objectId);

        $debts = $this->pivotObjectDebtService->getPivotDebtForObject($objectId);

        /* Долги подрядчикам */
        $contractorDebts = [
            'total_amount' => $debts['contractor']->total_amount,
            'details' => $debts['contractor']->debts
        ];

        $debtObjectImport = DebtImport::where('type_id', DebtImport::TYPE_OBJECT)->latest('date')->first();
        $objectExistInObjectImport = $debtObjectImport->debts()->where('object_id', $object->id)->count() > 0;

        if ($objectExistInObjectImport) {
            $contractorDebtsAvans = Debt::where('import_id', $debtObjectImport->id)->where('type_id', Debt::TYPE_CONTRACTOR)->where('object_id', $object->id)->sum('avans');
            $contractorDebtsGU = Debt::where('import_id', $debtObjectImport->id)->where('type_id', Debt::TYPE_CONTRACTOR)->where('object_id', $object->id)->sum('guarantee');
            $contractorDebts['total_amount'] += $contractorDebtsAvans + $contractorDebtsGU;
        }

        /* Долги поставщикам */
        $providerDebts = [
            'total_amount' => $debts['provider']->total_amount,
            'details' => $debts['provider']->debts
        ];

        /* Долги за услуги */
        $serviceDebts = [
            'total_amount' => $debts['service']->total_amount,
            'details' => $debts['service']->debts
        ];

        /* Долг на зарплаты ИТР */
        $ITRDebts = [
            'total_amount' => $object->getITRSalaryDebt(),
            'details' => []
        ];

        /* Долг на зарплаты рабочим */
        $workersDebts = [
            'total_amount' => $object->getWorkSalaryDebt(),
            'details' => $object->getWorkSalaryDebtDetails()
        ];

        $customerDebtInfo = [];
        $this->contractService->filterContracts(['object_id' => [$object->id]], $customerDebtInfo);

        return [
            'contractor_debts' => $contractorDebts,
            'provider_debts' => $providerDebts,
            'service_debts' => $serviceDebts,
            'itr_debts' => $ITRDebts,
            'workers_debts' => $workersDebts
        ];
    }
}