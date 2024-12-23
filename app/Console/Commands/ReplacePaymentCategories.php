<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\Payment;
use Exception;

class ReplacePaymentCategories extends HandledCommand
{
    protected $signature = 'oms:replace-payment-categories';

    protected $description = 'Заменяет старые категории оплат на новые';

    protected string $period = 'Вручную';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        $categories = [
            'Услуги' => Payment::CATEGORY_OPSTE,
            'Подрядчики' => Payment::CATEGORY_RAD,
            'Поставщики' => Payment::CATEGORY_MATERIAL,
        ];

        foreach ($categories as $old => $new) {
            $this->sendInfoMessage('Обработка "' . $old . '"');
            $this->sendInfoMessage('Найдено ' . Payment::where('category', $old)->count() . ' оплат');

            try {
                Payment::where('category', $old)->update(['category' => $new]);
            } catch(Exception $e) {
                $this->sendErrorMessage('Не удалось обновить категорию "' . $old . '": "' . $e->getMessage());
            }
        }

        $this->sendInfoMessage('Обработка завершена');

        $this->endProcess();

        return 0;
    }
}
