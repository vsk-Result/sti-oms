<?php

namespace App\Services;

use App\Imports\StatementImport;
use App\Models\Payment;
use App\Models\Statement;
use App\Models\Status;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class StatementService
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

    public function createStatement(array $requestData): null|Statement
    {
        $statement = Statement::create([
            'bank_id' => $requestData['bank_id'],
            'company_id' => $requestData['company_id'],
            'date' => $requestData['date'],
            'status_id' => Status::STATUS_BLOCKED,
            'file' => $this->uploadService->uploadFile('statements', $requestData['file'])
        ]);

        $statementData = $this->getStatementDataFromExcel($requestData['file']);

        if (empty($statementData)) {
            $this->destroyStatement($statement);
            return null;
        }

        $companyOrganization = $this->organizationService->getOrCreateOrganization([
            'company_id' => $statement->company->id,
            'name' => $statement->company->name,
            'inn' => $statement->company->inn,
            'kpp' => null
        ]);

        $processInfo = $this->processInfoFromStatementData($statement, $statementData);

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

            $this->paymentService->createPayment([
                'company_id' => $statement->company_id,
                'bank_id' => $statement->bank_id,
                'statement_id' => $statement->id,
                'object_id' => null,
                'object_worktype_id' => null,
                'organization_sender_id' => $organizationSender->id,
                'organization_receiver_id' => $organizationReceiver->id,
                'type_id' => Payment::TYPE_NONE,
                'payment_type_id' => Payment::PAYMENT_TYPE_NON_CASH,
                'code' => null,
                'category' => $this->paymentService->findCategoryFromDescription($payment['description']),
                'description' => $payment['description'],
                'date' => $statement->date,
                'amount' => $amount,
                'amount_without_nds' => $amount - $nds,
                'status_id' => Status::STATUS_BLOCKED
            ]);
        }

        $statement->update([
            'incoming_balance' => $processInfo['incoming_balance'],
            'outgoing_balance' => $processInfo['outgoing_balance']
        ]);

        $statement->reCalculateAmountsAndCounts();

        return $statement;
    }

    public function destroyStatement(Statement $statement): Statement
    {
        $statement->delete();

        foreach ($statement->payments as $payment) {
            $this->paymentService->destroyPayment($payment);
        }

        return $statement;
    }

    private function processInfoFromStatementData(Statement $statement, array $statementData): array
    {
        $class = $statement->getBankImportClass();
        return (new $class($this->paymentService))->processImportData($statementData);
    }

    private function getStatementDataFromExcel(UploadedFile $file): array
    {
        return Excel::toArray(new StatementImport(), $file);
    }
}
