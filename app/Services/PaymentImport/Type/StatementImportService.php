<?php

namespace App\Services\PaymentImport\Type;

use App\Imports\PaymentImport as PImport;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Models\PaymentImport;
use App\Models\Status;
use App\Services\CurrencyExchangeRateService;
use App\Services\OrganizationService;
use App\Services\PaymentService;
use App\Services\UploadService;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class StatementImportService
{
    private string $error;
    private PaymentService $paymentService;
    private OrganizationService $organizationService;
    private UploadService $uploadService;
    private CurrencyExchangeRateService $rateService;

    public function __construct(
        PaymentService $paymentService,
        UploadService $uploadService,
        OrganizationService $organizationService,
        CurrencyExchangeRateService $rateService
    ) {
        $this->error = '';
        $this->paymentService = $paymentService;
        $this->uploadService = $uploadService;
        $this->organizationService = $organizationService;
        $this->rateService = $rateService;
    }

    public function createImport(array $requestData): null|PaymentImport
    {
        $statementData = $this->getStatementDataFromExcel($requestData['file']);

        if (empty($statementData)) {
            return null;
        }

        $importCurrency = $requestData['currency'] ?? 'RUB';
        $importCurrencyRate = $importCurrency !== 'RUB'
            ? $this->rateService->getExchangeRate($requestData['date'], $importCurrency)->rate ?? 0
            : 1;

        $import = PaymentImport::create([
            'type_id' => is_null($requestData['bank_id']) ? PaymentImport::TYPE_PAYMENTS : PaymentImport::TYPE_STATEMENT,
            'bank_id' => $requestData['bank_id'],
            'company_id' => $requestData['company_id'],
            'date' => $requestData['date'],
            'status_id' => Status::STATUS_BLOCKED,
            'file' => $this->uploadService->uploadFile('payment-imports/statements', $requestData['file']),
            'description' => '',
            'currency' => $importCurrency,
            'currency_rate' => $importCurrencyRate,
        ]);

        $companyOrganization = $this->organizationService->getOrCreateOrganization([
            'company_id' => $import->company->id,
            'name' => $import->company->name,
            'inn' => $import->company->inn,
            'kpp' => null
        ]);

        $this->paymentService->loadCategoriesList();
        $processInfo = $this->processInfoFromStatementData($statementData);

        $codesWithoutWorktype = BObject::getCodesWithoutWorktype();

        foreach ($processInfo['payments'] as $payment) {
            if (str_contains($payment['organization_name'], 'НДС полученный')) {
                $payment['organization_name'] = 'Филиал "Центральный" Банка ВТБ (ПАО)';
            }

            if ($payment['pay_amount'] == 0) {
                $amount = $payment['receive_amount'];
                $organizationSender = $this->organizationService->getOrCreateOrganization([
                    'inn' => $payment['organization_inn'],
                    'name' => $payment['organization_name'],
                    'company_id' => null,
                    'kpp' => null
                ]);
                $organizationReceiver = $companyOrganization;
                $organizationCategory = $organizationSender->category;
            } else {
                $amount = $payment['pay_amount'];
                $organizationSender = $companyOrganization;
                $organizationReceiver = $this->organizationService->getOrCreateOrganization([
                    'inn' => $payment['organization_inn'],
                    'name' => $payment['organization_name'],
                    'company_id' => null,
                    'kpp' => null
                ]);
                $organizationCategory = $organizationReceiver->category;
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
                    $this->error = 'Объект "' . $code . '" не найден в системе. Загрузка не удалась.';
                    return $import;
                }

                $payment['type_id'] = Payment::TYPE_OBJECT;
                $payment['object_id'] = $object->id;
            }

            $category = $this->mapOldToNewCategory($payment['category'], $payment['description']);
            $category = empty($category) ? $organizationCategory : $category;
            $category = empty($category) ? $this->paymentService->findCategoryFromDescription($payment['description']) : $category;

            $payment = $this->paymentService->createPayment([
                'company_id' => $import->company_id,
                'bank_id' => $import->bank_id,
                'import_id' => $import->id,
                'object_id' => $payment['object_id'],
                'object_worktype_id' => $payment['object_worktype_id'],
                'organization_sender_id' => $organizationSender->id,
                'organization_receiver_id' => $organizationReceiver->id,
                'type_id' => $payment['type_id'],
                'payment_type_id' => is_null($import->bank_id) ? Payment::PAYMENT_TYPE_CASH : Payment::PAYMENT_TYPE_NON_CASH,
                'code' => $payment['code'] ?? null,
                'category' => $category,
                'description' => $payment['description'],
                'date' => ($import->company->short_name === 'БАМС' || is_null($import->bank_id)) ? $payment['date'] : $import->date,
                'amount' => $amount,
                'amount_without_nds' => $amount - $nds,
                'is_need_split' => $isNeedSplit,
                'status_id' => Status::STATUS_BLOCKED,
                'currency' => $importCurrency,
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

    public function updateImport(PaymentImport $statement, array $requestData): null|PaymentImport
    {
        if (
            $statement->date == $requestData['date'] &&
            $statement->company_id == $requestData['company_id'] &&
            $statement->bank_id == $requestData['bank_id']
        ) {
            return null;
        }

        $statement->update([
            'bank_id' => (int) $requestData['bank_id'],
            'company_id' => (int) $requestData['company_id'],
            'date' => $requestData['date'],
        ]);

        foreach ($statement->payments as $payment) {
            $this->paymentService->updatePayment($payment, [
                'company_id' => $statement->company_id,
                'bank_id' => $statement->bank_id,
                'date' => ($statement->company->short_name === 'БАМС' || is_null($statement->bank_id)) ? $payment->date : $statement->date,
            ]);
        }

        return null;
    }

    public function processInfoFromStatementData(array $statementData): array
    {
        $returnData = [];
        $importData = $statementData[0];

        $returnData['incoming_balance'] = (float) preg_replace("/[^-.0-9]/", '', $importData[2][0]);
        $returnData['outgoing_balance'] = (float) preg_replace("/[^-.0-9]/", '', $importData[2][1]);

        $additionInfo = [];
        if (isset($statementData[1]) && count($statementData[1]) > 1) {
            foreach ($statementData[1] as $rowNum => $rowData) {
                if ($rowNum === 0 || empty($rowData[1])) {
                    continue;
                }

                $description = $this->cleanValue($rowData[0]);
                $description = str_replace(' ', '', $description);
                $comment = $this->cleanValue($rowData[1]);
                $comment = str_replace(' ', '', $comment);

                $additionInfo[$description]['object'] = $comment;
                $additionInfo[$description]['code'] = '';

                if (str_contains($comment, '/')) {
                    $additionInfo[$description]['object'] = substr($comment, 0 , strpos($comment, '/'));
                    $additionInfo[$description]['code'] = substr($comment, strpos($comment, '/') + 1);
                    $additionInfo[$description]['category'] = isset($rowData[2]) ? trim($rowData[2]) : '';
                }
            }
        }

        $isNotEmptyAdditionInfo = count($additionInfo) > 0;

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

            $object = $rowData[0];
            $object = str_replace(',', '.', $object);
            $code = $rowData[1];
            $category = '';

            if ($isNotEmptyAdditionInfo) {
                $cleanDescription = str_replace(' ', '', $description);
                $cleanDescription = mb_substr($cleanDescription, 0, 80);

                foreach ($additionInfo as $key => $value) {
                    if (str_contains($key, $cleanDescription)) {
                        $category = $value['category'] ?? '';

                        if (empty($object)) {
                            $object = $value['object'];
                        }
                        if (empty($code)) {
                            $code = $value['code'];
                        }

                        break;
                    }
                }
            }

            $date = Carbon::parse(Date::excelToDateTimeObject($rowData[2]))->format('Y-m-d');

            $inn = null;
            if (isset($rowData[6])) {
                $inn = $this->cleanValue($rowData[6]);
            }

            $returnData['payments'][] = [
                'object' => $object,
                'code' => $code,
                'date' => $date,
                'pay_amount' => $payAmount,
                'receive_amount' => $receiveAmount,
                'organization_name' => $this->cleanValue($rowData[3]),
                'organization_inn' => $inn,
                'description' => $description,
                'category' => $category,
            ];
        }

        return $returnData;
    }

    private function cleanValue($value): string
    {
        return str_replace("\n", '', (string) $value);
    }

    public function getStatementDataFromExcel(UploadedFile $file): array
    {
        return Excel::toArray(new PImport(), $file);
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function hasError(): bool
    {
        return ! empty($this->error);
    }

    private function mapOldToNewCategory($category, $description)
    {
        if (empty($category)) {
            return $category;
        }

        $map = [
            'физические лица' => Payment::CATEGORY_OPSTE,
            'услуги' => Payment::CATEGORY_OPSTE,
            'подрядчики' => Payment::CATEGORY_RAD,
            'субподрядчик' => Payment::CATEGORY_RAD,
            'поставщики' => Payment::CATEGORY_MATERIAL,
        ];

        $resultCategory = $map[mb_strtolower($category)] ?? $category;

        if (mb_strtolower($category) === 'физические лица') {
            if (mb_strpos($description, 'аренд') === false) {
                $resultCategory = Payment::CATEGORY_MATERIAL;
            }
        }

        return $resultCategory;
    }
}
