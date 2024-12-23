<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Services\OrganizationTransferPaymentsService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class TransferOrganizationsPayments extends HandledCommand
{
    protected $signature = 'oms:transfer-organizations-payments-from-excel';

    protected $description = 'Переносит оплаты с одного контрагента на другого из Excel';

    protected string $period = 'Ежедневно в 13:00 и в 18:00';

    public function __construct(private OrganizationTransferPaymentsService $organizationTransferPaymentsService)
    {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        $importFilePath = storage_path() . '/app/public/public/transfer_organizations_payments.xlsx';

        if (! File::exists($importFilePath)) {
            $this->sendErrorMessage('Файл для загрузки "' . $importFilePath . '" не найден');
            $this->endProcess();
            return 0;
        }

        try {
            $importResult = $this->organizationTransferPaymentsService->transfer(new UploadedFile($importFilePath, 'transfer_organizations_payments.xlsx'));
        } catch (\Exception $e) {
            $this->sendErrorMessage('Не удалось загрузить файл: ' . $e->getMessage());
            $this->endProcess();
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

        $this->sendInfoMessage($message);

        $this->endProcess();

        return 0;
    }
}
