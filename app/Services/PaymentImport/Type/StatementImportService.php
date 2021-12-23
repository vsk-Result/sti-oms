<?php

namespace App\Services\PaymentImport\Type;

use App\Imports\PaymentImport as PImport;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Models\PaymentImport;
use App\Models\Status;
use App\Services\ObjectService;
use App\Services\OrganizationService;
use App\Services\PaymentService;
use App\Services\UploadService;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class StatementImportService
{
    private PaymentService $paymentService;
    private OrganizationService $organizationService;
    private UploadService $uploadService;
    private ObjectService $objectService;

    public function __construct(
        PaymentService $paymentService,
        UploadService $uploadService,
        OrganizationService $organizationService,
        ObjectService $objectService,
    ) {
        $this->paymentService = $paymentService;
        $this->uploadService = $uploadService;
        $this->organizationService = $organizationService;
        $this->objectService = $objectService;
    }

    public function createImport(array $requestData): null|PaymentImport
    {
        $statementData = $this->getStatementDataFromExcel($requestData['file']);

//        $result = [];
//        foreach ($statementData[0] as $index => $row) {
//            if ($index == 0) continue;
//            if (empty($row[1])) break;
//            $description = empty($row[0]) ? '' : $row[0];
//            $result[] = $this->paymentService->checkHasNDSFromDescription($description) ? round($row[1] / 6, 2) : 0;
//        }
//
//        dd($result);

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
            'description' => ''
        ]);

        $companyOrganization = $this->organizationService->getOrCreateOrganization([
            'company_id' => $import->company->id,
            'name' => $import->company->name,
            'inn' => $import->company->inn,
            'kpp' => null
        ]);

        $this->paymentService->loadCategoriesList();
        $processInfo = $this->processInfoFromStatementData($statementData);

        foreach ($processInfo['payments'] as $payment) {
            if (str_contains($payment['organization_name'], 'НДС полученный')) {
                $payment['organization_name'] = 'Филиал "Центральный" Банка ВТБ (ПАО)';
            }

            if ($payment['pay_amount'] == 0) {
                $amount = $payment['receive_amount'];
                $organizationSender = $this->organizationService->getOrCreateOrganization([
                    'inn' => null,
                    'name' => $payment['organization_name'],
                    'company_id' => null,
                    'kpp' => null
                ]);
                $organizationReceiver = $companyOrganization;
            } else {
                $amount = $payment['pay_amount'];
                $organizationSender = $companyOrganization;
                $organizationReceiver = $this->organizationService->getOrCreateOrganization([
                    'inn' => null,
                    'name' => $payment['organization_name'],
                    'company_id' => null,
                    'kpp' => null
                ]);
            }

            $nds = $this->paymentService->checkHasNDSFromDescription($payment['description'])
                ? round($amount / 6, 2)
                : 0;

            $isNeedSplit = $this->paymentService->checkIsNeedSplitFromDescription($payment['description']);

            $payment['object_id'] = null;
            $payment['object_worktype_id'] = null;
            $payment['type_id'] = Payment::TYPE_NONE;

            if ($payment['object'] === 'Трансфер') {
                $payment['type_id'] = Payment::TYPE_TRANSFER;
            } elseif ($payment['object'] === 'Общее') {
                $payment['type_id'] = Payment::TYPE_GENERAL;
            } elseif (! empty($payment['object'])) {
                if (str_contains($payment['object'], ',')) {
                    $payment['object'] = str_replace(',', '.', $payment['object']);
                }

                if ($payment['object'] == '27' || $payment['object'] == '27.1' || $payment['object'] == '27.7') {
                    $code = '1';
                } elseif ($payment['object'] == '27.2') {
                    $code = '2';
                } elseif ($payment['object'] == '27.3') {
                    $code = '4';
                } elseif ($payment['object'] == '27.4') {
                    $code = '3';
                } elseif ($payment['object'] == '27.8') {
                    $code = '5';
                } elseif ($payment['object'] == '28') {
                    $code = '28';
                } else {
                    $code = $payment['object'];
                    $code = substr($code, 0, strpos($code, '.'));

                    if (str_contains($payment['object'], '.')) {
                        $payment['object_worktype_id'] = (int) substr($payment['object'], strpos($payment['object'], '.') + 1);
                    }
                }

                $object = BObject::where('code', $code)->first();

                if (! $object) {
                    $object = $this->objectService->createObject([
                        'code' => $code,
                        'name' => 'Без названия',
                        'address' => null,
                        'responsible_name' => null,
                        'responsible_email' => null,
                        'responsible_phone' => null,
                        'photo' => null,
                    ]);
                }

                $payment['type_id'] = Payment::TYPE_OBJECT;
                $payment['object_id'] = $object->id;
            }

            $payment = $this->paymentService->createPayment([
                'company_id' => $import->company_id,
                'bank_id' => $import->bank_id,
                'import_id' => $import->id,
                'object_id' => $payment['object_id'],
                'object_worktype_id' => $payment['object_worktype_id'],
                'organization_sender_id' => $organizationSender->id,
                'organization_receiver_id' => $organizationReceiver->id,
                'type_id' => $payment['type_id'],
                'payment_type_id' => Payment::PAYMENT_TYPE_NON_CASH,
                'code' => $payment['code'] ?? null,
                'category' => $this->paymentService->findCategoryFromDescription($payment['description']),
                'description' => $payment['description'],
                'date' => $import->company->short_name === 'БАМС' ? $payment['date'] : $import->date,
                'amount' => $amount,
                'amount_without_nds' => $amount - $nds,
                'is_need_split' => $isNeedSplit,
                'status_id' => Status::STATUS_BLOCKED
            ]);

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
        }

        $import->update([
            'incoming_balance' => $processInfo['incoming_balance'],
            'outgoing_balance' => $processInfo['outgoing_balance']
        ]);

        $import->reCalculateAmountsAndCounts();

        return $import;
    }

    private function processInfoFromStatementData(array $statementData): array
    {
        $returnData = [];
        $importData = $statementData[0];

        $returnData['incoming_balance'] = (float) preg_replace("/[^-.0-9]/", '', $importData[2][0]);
        $returnData['outgoing_balance'] = (float) preg_replace("/[^-.0-9]/", '', $importData[2][1]);

        foreach ($importData as $rowNum => $rowData) {

            if ($rowNum < 5) {
                continue;
            }

            if (is_null($rowData[4])) {
                break;
            }

            $description = $this->cleanValue($rowData[4]);
            $amount = (float) $rowData[5];

            if ($amount < 0) {
                $payAmount = $amount;
                $receiveAmount = 0;
            } else {
                $payAmount = 0;
                $receiveAmount = $amount;
            }

            $returnData['payments'][] = [
                'object' => $rowData[0],
                'code' => $rowData[1],
                'date' => Carbon::parse(Date::excelToDateTimeObject($rowData[2]))->format('Y-m-d'),
                'pay_amount' => $payAmount,
                'receive_amount' => $receiveAmount,
                'organization_name' => $this->cleanValue($rowData[3]),
                'description' => $description,
            ];
        }

        return $returnData;
    }

    private function cleanValue($value): string
    {
        return str_replace("\n", '', (string) $value);
    }

    private function getStatementDataFromExcel(UploadedFile $file): array
    {
        return Excel::toArray(new PImport(), $file);
    }
}
