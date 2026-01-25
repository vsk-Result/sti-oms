<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\Contract\Act;
use Carbon\Carbon;

class UpdateActPeriodFromDate extends HandledCommand
{
    protected $signature = 'oms:update-act-period-from-date';

    protected $description = 'Заполняет пустые поля отчетного периода актов по дате';

    protected string $period = 'Вручную';

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        $this->sendInfoMessage('Старт заполнения отчетного периода актов');

        $acts = Act::whereNull('period')->get();

        foreach ($acts as $act) {
            if (empty($act->date)) {
                continue;
            }

            $period = translate_year_month_word(Carbon::parse($act->date)->format('F Y'));

            $act->update([
                'period' => $period . '::' . $period
            ]);
        }

        $this->sendInfoMessage('Заполнение актов завершено');

        $this->endProcess();

        return 0;
    }
}
