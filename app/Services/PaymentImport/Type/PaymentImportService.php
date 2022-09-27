<?php

namespace App\Services\PaymentImport\Type;

use App\Imports\PaymentImport as PImport;
use App\Models\Company;
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

class PaymentImportService
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
        $importData = Excel::toArray(new PImport(), $requestData['file']);

        if (empty($importData)) {
            return null;
        }

        $import = PaymentImport::create([
            'type_id' => PaymentImport::TYPE_PAYMENTS,
            'bank_id' => $requestData['bank_id'],
            'company_id' => $requestData['company_id'],
            'date' => Carbon::now(),
            'status_id' => Status::STATUS_BLOCKED,
            'file' => $this->uploadService->uploadFile('payment-imports/payments', $requestData['file']),
            'description' => $requestData['description'],
        ]);

        $companyOrganization = $this->organizationService->getOrCreateOrganization([
            'company_id' => $import->company->id,
            'name' => $import->company->name,
            'inn' => $import->company->inn,
            'kpp' => null
        ]);

        $this->paymentService->loadCategoriesList();
        $processInfo = $this->processInfoFromImportData($importData);

        $codesWithoutWorktype = BObject::getCodesWithoutWorktype();

        foreach ($processInfo['payments'] as $payment) {

            if (isset($payment['company_id'])) {
                $company = Company::find($payment['company_id']);
                $companyOrganization = $this->organizationService->getOrCreateOrganization([
                    'company_id' => $company->id,
                    'name' => $company->name,
                    'inn' => $company->inn,
                    'kpp' => null
                ]);
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

                if (isset($codesWithoutWorktype[$payment['object']])) {
                    $code = $codesWithoutWorktype[$payment['object']];
                } else {
                    $code = $payment['object'];

                    if (str_contains($payment['object'], '.')) {
                        $code = substr($code, 0, strpos($code, '.'));
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
                'company_id' => $payment['company_id'] ?? $import->company_id,
                'bank_id' => $import->bank_id,
                'import_id' => $import->id,
                'object_id' => $payment['object_id'],
                'object_worktype_id' => $payment['object_worktype_id'],
                'organization_sender_id' => $organizationSender->id,
                'organization_receiver_id' => $organizationReceiver->id,
                'type_id' => $payment['type_id'],
                'payment_type_id' => $payment['payment_type_id'] ?? Payment::PAYMENT_TYPE_NON_CASH,
                'code' => $payment['code'] ?? null,
                'category' => $this->paymentService->findCategoryFromDescription($payment['description']),
                'description' => $payment['description'],
                'date' => $payment['date'],
                'amount' => $amount,
                'amount_without_nds' => $amount - $nds,
                'is_need_split' => false,
                'status_id' => Status::STATUS_BLOCKED,
                'parameters' => $payment['parameters'] ?? []
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

        $import->reCalculateAmountsAndCounts();

        return $import;
    }

    private function processInfoFromImportData(array $importData): array
    {
        $returnData = [];
        $importData = $importData[0];

        foreach ($importData as $rowNum => $rowData) {

            if ($rowNum === 0) {
                continue;
            }

            if (count($rowData) === 12) {
                if ($rowData[6] === 'у') {
                    $payment = Payment::find($rowData[9]);
                    if ($payment) {
                        $this->paymentService->destroyPayment($payment);
                    }
                } elseif ($rowData[6] === 'и') {
                    $payment = Payment::find($rowData[9]);
                    if ($payment) {
                        $parameters = [];

                        if ($rowData[7] === 'з') {
                            $parameters['transfer_font_color'] = '#60bd60';
                        } elseif ($rowData[7] === 'к') {
                            $parameters['transfer_font_color'] = '#ff0000';
                        }

                        if ($rowData[8] === 'з') {
                            $parameters['transfer_background_color'] = '#d7ffb7';
                        } elseif ($rowData[8] === 'ж') {
                            $parameters['transfer_background_color'] = '#fdfd6b';
                        }

                        $payment->update([
                            'parameters' => $parameters
                        ]);
                    }
                } elseif ($rowData[6] === 'д') {
                    $description = $this->cleanValue($rowData[4]);
                    $amount = (float) $rowData[5];

                    if ($amount < 0) {
                        $payAmount = $amount;
                        $receiveAmount = 0;
                    } else {
                        $payAmount = 0;
                        $receiveAmount = $amount;
                    }

                    $parameters = [];

                    if ($rowData[7] === 'з') {
                        $parameters['transfer_font_color'] = '#60bd60';
                    } elseif ($rowData[7] === 'к') {
                        $parameters['transfer_font_color'] = '#ff0000';
                    }

                    if ($rowData[8] === 'з') {
                        $parameters['transfer_background_color'] = '#d7ffb7';
                    } elseif ($rowData[8] === 'ж') {
                        $parameters['transfer_background_color'] = '#fdfd6b';
                    }

                    $object = $rowData[0];
                    $object = str_replace(',', '.', $object);

                    $returnData['payments'][] = [
                        'object' => $object,
                        'code' => $rowData[1],
                        'date' => Carbon::parse(Date::excelToDateTimeObject($rowData[2]))->format('Y-m-d'),
                        'pay_amount' => $payAmount,
                        'receive_amount' => $receiveAmount,
                        'organization_name' => $this->cleanValue($rowData[3]),
                        'description' => $description,
                        'parameters' => $parameters,
                        'company_id' => $rowData[11],
                        'bank_id' => $rowData[10] === Payment::PAYMENT_TYPE_NON_CASH ? $rowData[12] : null,
                        'payment_type_id' => $rowData[10],
                    ];
                }
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

            $object = $rowData[0];
            $object = str_replace(',', '.', $object);

            $returnData['payments'][] = [
                'object' => $object,
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
}
