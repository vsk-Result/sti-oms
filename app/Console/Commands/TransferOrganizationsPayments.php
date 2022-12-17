<?php

namespace App\Console\Commands;

use App\Services\OrganizationTransferPaymentsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class TransferOrganizationsPayments extends Command
{
    protected $signature = 'oms-imports:transfer-organizations-payments-from-excel';

    protected $description = 'Переносит оплаты с одного контрагента на другого из Excel';

    private OrganizationTransferPaymentsService $organizationTransferPaymentsService;

    public function __construct(OrganizationTransferPaymentsService $organizationTransferPaymentsService)
    {
        parent::__construct();
        $this->organizationTransferPaymentsService = $organizationTransferPaymentsService;
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Автоматический перенос оплат между контрагентами из Excel');

        $importFilePath = storage_path() . '/app/public/public/transfer_organizations_payments.xlsx';

        if (! File::exists($importFilePath)) {
            Log::channel('custom_imports_log')->debug('[ERROR] Файл для загрузки "' . $importFilePath . '" не найден');
            return 0;
        }

        try {
            $importStatus = $this->organizationTransferPaymentsService->transfer(new UploadedFile($importFilePath, 'transfer_organizations_payments.xlsx'));
        } catch (\Exception $e) {
            Log::channel('custom_imports_log')->debug('[ERROR] Не удалось загрузить файл: "' . $e->getMessage());
            return 0;
        }

        if ($importStatus !== 'ok') {
            Log::channel('custom_imports_log')->debug('[ERROR] Не удалось загрузить файл: "' . $importStatus);
            return 0;
        }

        Log::channel('custom_imports_log')->debug('[SUCCESS] Файл успешно загружен');

        return 0;
    }
}
