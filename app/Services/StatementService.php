<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Imports\StatementImport;
use App\Models\Company;
use App\Models\CRM\AvansImport;
use App\Models\Object\BObject;
use App\Models\Object\WorkType;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\Statement;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class StatementService
{
    private PaymentService $paymentService;
    private OrganizationService $organizationService;
    private UploadService $uploadService;
    private Sanitizer $sanitizer;

    public function __construct(
        PaymentService $paymentService,
        UploadService $uploadService,
        OrganizationService $organizationService,
        Sanitizer $sanitizer,
    ) {
        $this->paymentService = $paymentService;
        $this->uploadService = $uploadService;
        $this->organizationService = $organizationService;
        $this->sanitizer = $sanitizer;
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

            $isNeedSplit = $this->paymentService->checkIsNeedSplitFromDescription($payment['description']);

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
                'is_need_split' => $isNeedSplit,
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

    public function splitPayment(Statement $statement, Payment $payment, array $request): Collection
    {
        $import = AvansImport::find($request['crm_avans_import_id']);
        $import->load('items', 'items.avans');

        $amountGroupedByObjectCode = [];
        foreach ($import->items as $item) {
            if (! isset($amountGroupedByObjectCode[$item->avans->code])) {
                $amountGroupedByObjectCode[$item->avans->code] = 0;
            }
            $amountGroupedByObjectCode[$item->avans->code] += $item->avans->value;
        }

        $description = $this->sanitizer->set($import->description)->lowerCase()->get();
        $costCode = str_contains($description, 'зарплат') ? '7.17' : '7.26';

        $company = Company::find(1);
        $organizationSenderId = $company->organizations()->first()->id;
        $organizationReceiverId = Organization::where('name', 'ФИЛИАЛ № 7701 БАНКА ВТБ (ПАО) Г. МОСКВА')->first()->id;

        $payments = [];
        foreach ($amountGroupedByObjectCode as $code => $amount) {
            $objectCode = substr($code, 0, strpos($code, '.'));
            $worktypeCode = (int) substr($code, strpos($code, '.') + 1);
            $payments[] = $this->paymentService->createPayment([
                'company_id' => $company->id,
                'bank_id' => 1,
                'statement_id' => $statement->id,
                'object_id' => BObject::where('code', $objectCode)->first()->id ?? null,
                'object_worktype_id' => WorkType::getIdByCode($worktypeCode),
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

        $this->paymentService->destroyPayment($payment);
        $statement->reCalculateAmountsAndCounts();

        return collect($payments)->collect();
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
