<?php

namespace App\Services;

use App\Models\FinanceReportHistory;
use App\Models\Object\BObject;
use Carbon\Carbon;

class ObjectCheckService
{
    private UploadDebtStatusService $uploadDebtStatusService;

    public function __construct(UploadDebtStatusService $uploadDebtStatusService)
    {
        $this->uploadDebtStatusService = $uploadDebtStatusService;
    }

    public function getCheckStatusInfo(BObject $object): array
    {
        $checkInfo = [
            'customer_payments' => $this->getReceiveCheckInfo($object),
            'files_debts' => $this->getUploadDebtFilesInfo($object),
        ];

        $hasWarning = false;
        foreach ($checkInfo as $info) {
            $hasWarning = $hasWarning || $info['has_warning'];
        }

        $checkInfo['has_warning'] = $hasWarning;

        return $checkInfo;
    }

    public function getReceiveCheckInfo(BObject $object): array
    {
        $info = FinanceReportHistory::getCurrentFinanceReportForObject($object);

        $receiveSum = $info['receive_customer_fix_avans'] + $info['receive_customer_target_avans'] + $info['receive_customer_acts'] + $info['receive_customer_gu'];

        return [
            'has_warning' => is_valid_amount_in_range($receiveSum !== $info['receive_customer']),
            'diff_amount' => $receiveSum - $info['receive_customer'],
            'payments_receive_amount' => $info['receive_customer'],
            'avanses_receive_amount' => $info['receive_customer_fix_avans'] + $info['receive_customer_target_avans'],
            'acts_receive_amount' => $info['receive_customer_acts'],
            'gu_receive_amount' => $info['receive_customer_gu'],
        ];
    }

    public function getUploadDebtFilesInfo(BObject $object): array
    {
        $updatedInfo = $this->uploadDebtStatusService->getUpdatedInfoForObject($object);
        $linksInfo = $this->uploadDebtStatusService->getLinksInfoForObject($object);

        $nowFormatted = Carbon::now()->format('d.m.Y');
        $yesterdayFormatted = Carbon::yesterday()->format('d.m.Y');
        $contractorsFileUploadDateWrong = $updatedInfo['contractors'] !== $nowFormatted && $updatedInfo['contractors'] !== $yesterdayFormatted;
        $providersFileUploadDateWrong = $updatedInfo['providers'] !== $nowFormatted && $updatedInfo['providers'] !== $yesterdayFormatted;
        $servicesFileUploadDateWrong = $updatedInfo['service'] !== $nowFormatted && $updatedInfo['service'] !== $yesterdayFormatted;

        return [
            'has_warning' => $contractorsFileUploadDateWrong || $providersFileUploadDateWrong || $servicesFileUploadDateWrong,
            'object_contractors' => [
                'link' => $linksInfo['contractors'],
                'uploaded_date' => $updatedInfo['contractors'],
                'has_warning' => $contractorsFileUploadDateWrong
            ],
            '1c_providers' => [
                'link' => $linksInfo['providers'],
                'uploaded_date' => $updatedInfo['providers'],
                'has_warning' => $providersFileUploadDateWrong
            ],
            '1c_services' => [
                'link' => $linksInfo['service'],
                'uploaded_date' => $updatedInfo['service'],
                'has_warning' => $servicesFileUploadDateWrong
            ],
        ];
    }
}
