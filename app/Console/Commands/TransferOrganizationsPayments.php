<?php

namespace App\Console\Commands;

use App\Console\BaseNotifyCommand;
use App\Services\OrganizationTransferPaymentsService;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class TransferOrganizationsPayments extends BaseNotifyCommand
{
    protected $signature = 'oms-imports:transfer-organizations-payments-from-excel';

    protected $description = 'Переносит оплаты с одного контрагента на другого из Excel';

    private OrganizationTransferPaymentsService $organizationTransferPaymentsService;

    public function __construct(OrganizationTransferPaymentsService $organizationTransferPaymentsService)
    {
        parent::__construct();
        $this->commandName = 'Удаление дублей и перенос данных между контрагентами';
        $this->organizationTransferPaymentsService = $organizationTransferPaymentsService;
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Автоматический перенос оплат между контрагентами из Excel');

        $importFilePath = storage_path() . '/app/public/public/transfer_organizations_payments.xlsx';

        if (! File::exists($importFilePath)) {
            $this->sendErrorNotification('Файл для загрузки "' . $importFilePath . '" не найден');
            return 0;
        }

        try {
            $importResult = $this->organizationTransferPaymentsService->transfer(new UploadedFile($importFilePath, 'transfer_organizations_payments.xlsx'));
        } catch (\Exception $e) {
            $this->sendErrorNotification('Не удалось загрузить файл: ' . $e->getMessage());
            return 0;
        }

        $message = '';

        if (count($importResult) > 0) {
            foreach ($importResult as $item) {
                $message .= $item['old'] . ' => ' . $item['new'] . PHP_EOL;
            }
        } else {
            $message.= 'Обработка прошла без изменений';
        }

        $this->sendSuccessNotification($message);
        return 0;
    }
}
