<?php

namespace App\Services\PaymentImport\Type;

use App\Imports\PaymentImport as PImport;
use App\Models\Payment;
use App\Models\PaymentImport;
use App\Models\Status;
use App\Services\OrganizationService;
use App\Services\PaymentService;
use App\Services\UploadService;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class StatementImportService
{
    private PaymentService $paymentService;
    private OrganizationService $organizationService;
    private UploadService $uploadService;

    public function __construct(
        PaymentService $paymentService,
        UploadService $uploadService,
        OrganizationService $organizationService,
    ) {
        $this->paymentService = $paymentService;
        $this->uploadService = $uploadService;
        $this->organizationService = $organizationService;
    }

    public function createImport(array $requestData): null|PaymentImport
    {
        $statementData = $this->getStatementDataFromExcel($requestData['file']);

        if (empty($statementData)) {
            return null;
        }

        $import = PaymentImport::create([
            'type_id' => PaymentImport::TYPE_STATEMENT,
            'bank_id' => $requestData['bank_id'],
            'company_id' => $requestData['company_id'],
            'date' => $requestData['date'],
            'status_id' => Status::STATUS_BLOCKED,
            'file' => $this->uploadService->uploadFile('payment-imports/statements', $requestData['file']),
            'description' => $requestData['description']
        ]);

        $companyOrganization = $this->organizationService->getOrCreateOrganization([
            'company_id' => $import->company->id,
            'name' => $import->company->name,
            'inn' => $import->company->inn,
            'kpp' => null
        ]);

        $this->paymentService->loadCategoriesList();
        $processInfo = $this->processInfoFromStatementData($import, $statementData);

        foreach ($processInfo['payments'] as $payment) {

            if ($payment['pay_amount'] == 0) {
                $amount = $payment['receive_amount'];
                $organizationSender = $this->organizationService->getOrCreateOrganization([
                    'inn' => $payment['organization_sender_inn'],
                    'name' => $payment['organization_name'],
                    'company_id' => null,
                    'kpp' => null
                ]);
                $organizationReceiver = $companyOrganization;
            } else {
                $amount = $payment['pay_amount'];
                $organizationSender = $companyOrganization;
                $organizationReceiver = $this->organizationService->getOrCreateOrganization([
                    'inn' => $payment['organization_receiver_inn'],
                    'name' => $payment['organization_name'],
                    'company_id' => null,
                    'kpp' => null
                ]);
            }

            $nds = $this->paymentService->checkHasNDSFromDescription($payment['description'])
                ? round($amount / 6, 2)
                : 0;

            $isNeedSplit = $this->paymentService->checkIsNeedSplitFromDescription($payment['description']);

            $this->paymentService->createPayment([
                'company_id' => $import->company_id,
                'bank_id' => $import->bank_id,
                'import_id' => $import->id,
                'object_id' => null,
                'object_worktype_id' => null,
                'organization_sender_id' => $organizationSender->id,
                'organization_receiver_id' => $organizationReceiver->id,
                'type_id' => Payment::TYPE_NONE,
                'payment_type_id' => Payment::PAYMENT_TYPE_NON_CASH,
                'code' => null,
                'category' => $this->paymentService->findCategoryFromDescription($payment['description']),
                'description' => $payment['description'],
                'date' => $import->date,
                'amount' => $amount,
                'amount_without_nds' => $amount - $nds,
                'is_need_split' => $isNeedSplit,
                'status_id' => Status::STATUS_BLOCKED
            ]);
        }

        $import->update([
            'incoming_balance' => $processInfo['incoming_balance'],
            'outgoing_balance' => $processInfo['outgoing_balance']
        ]);

        $import->reCalculateAmountsAndCounts();

        return $import;
    }

    private function processInfoFromStatementData(PaymentImport $import, array $statementData): array
    {
        $class = $import->getBankImportClass();
        return (new $class($this->paymentService))->processImportData($statementData);
    }

    private function getStatementDataFromExcel(UploadedFile $file): array
    {
        return Excel::toArray(new PImport(), $file);
    }
}
