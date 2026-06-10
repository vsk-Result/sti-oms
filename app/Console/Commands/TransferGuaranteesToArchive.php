<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\Guarantee;
use App\Models\Status;

class TransferGuaranteesToArchive extends HandledCommand
{
    protected $signature = 'oms:transfer-guarantees-to-archive';

    protected $description = 'Очищает статьи затрат оплат';

    protected string $period = 'Вручную';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
//        if ($this->isProcessRunning()) {
//            return 0;
//        }
//
//        $this->startProcess();

        $guarantees = Guarantee::where('status_id', Status::STATUS_ACTIVE)->get();

        foreach ($guarantees as $guarantee) {
            if (!is_valid_amount_in_range($guarantee->fact_amount - $guarantee->amount_payments)) {
                $guarantee->update([
                    'status_id' => Status::STATUS_BLOCKED
                ]);
            }
        }
//        $this->endProcess();

        return 0;
    }
}
