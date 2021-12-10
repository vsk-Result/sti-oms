<?php

namespace App\Services\PaymentImport\Type;

use App\Imports\PaymentImport as PImport;
use App\Models\Company;
use App\Models\Payment;
use App\Models\PaymentImport;
use App\Models\Status;
use App\Services\OrganizationService;
use App\Services\PaymentService;
use App\Services\UploadService;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class HistoryImportImportService
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
        $historyData = $this->getHistoryDataFromExcel($requestData['file']);

        if (empty($historyData)) {
            return null;
        }

        $import = PaymentImport::create([
            'type_id' => PaymentImport::TYPE_HISTORY,
            'bank_id' => null,
            'company_id' => 1,
            'date' => Carbon::now()->format('Y-m-d'),
            'status_id' => Status::STATUS_ACTIVE,
            'file' => $this->uploadService->uploadFile('payment-imports/history', $requestData['file']),
            'description' => 'Загружен файл ' . $requestData['file']->getClientOriginalName()
        ]);

        $companyOrganization = $this->organizationService->getOrCreateOrganization([
            'company_id' => $import->company->id,
            'name' => $import->company->name,
            'inn' => $import->company->inn,
            'kpp' => null
        ]);

        $this->paymentService->loadCategoriesList();
        foreach ($historyData[0] as $index => $paymentData) {
            if ($index === 0) {
                continue;
            }

            $date = Carbon::parse(Date::excelToDateTimeObject($paymentData[2]))->format('Y-m-d');
            $amount = (float) $paymentData[6];

            if ($amount == 0) {
                continue;
            }

            $description = $paymentData[12] ?? '';

            $issetPayment = false;
            if (empty($paymentData[12])) {
                $issetPayment = Payment::where('date', $date)
                    ->where('object_id', $requestData['object_id'])
                    ->where('amount', $amount)
                    ->where('description', $description)
                    ->first();
            }



            $companyId = $paymentData[13] === 'КАССА' ? 1 : Company::where('short_name', $paymentData[13])->first()->id;

            if ($amount > 0) {
                $organizationSender = $this->organizationService->getOrCreateOrganization([
                    'inn' => null,
                    'name' => $paymentData[11],
                    'company_id' => null,
                    'kpp' => null
                ]);
                $organizationReceiver = $companyOrganization;
            } else {
                $organizationSender = $companyOrganization;
                $organizationReceiver = $this->organizationService->getOrCreateOrganization([
                    'inn' => null,
                    'name' => $paymentData[11],
                    'company_id' => null,
                    'kpp' => null
                ]);
            }

            $nds = $this->paymentService->checkHasNDSFromDescription($description)
                ? round($amount / 6, 2)
                : 0;

            $this->paymentService->createPayment([
                'company_id' => $companyId,
                'bank_id' => null,
                'import_id' => $import->id,
                'object_id' => (int) $requestData['object_id'],
                'object_worktype_id' => (int) $paymentData[3],
                'organization_sender_id' => $organizationSender->id,
                'organization_receiver_id' => $organizationReceiver->id,
                'type_id' => Payment::TYPE_OBJECT,
                'payment_type_id' => $paymentData[13] === 'КАССА' ? Payment::PAYMENT_TYPE_CASH : Payment::PAYMENT_TYPE_NON_CASH,
                'code' => $paymentData[4],
                'category' => $paymentData[14],
                'description' => $description,
                'date' => $date,
                'amount' => $amount,
                'amount_without_nds' => $amount - $nds,
                'is_need_split' => false,
                'status_id' => $issetPayment ? Status::STATUS_BLOCKED : Status::STATUS_ACTIVE
            ]);
        }

        $import->reCalculateAmountsAndCounts();

        return $import;
    }

    private function getHistoryDataFromExcel(UploadedFile $file): array
    {
        return Excel::toArray(new PImport(), $file);
    }
}
