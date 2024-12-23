<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\CRM\Cost;
use Carbon\Carbon;

class NotifyToEmailAboutUnclosedCRMCostClosures extends HandledCommand
{
    protected $signature = 'oms:notify-to-email-about-unclosed-crm-cost-closures';

    protected $description = 'Отправляет на почту пользователям CRM просьбу закрыть кассу';

    protected string $period = 'Ежедневно в 10:00';

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

        $closures = [];
        $nowMonth = Carbon::now()->format('Y-m') . '-01';
        $costs = Cost::where('is_archive', false)->with('closures')->get();
        $notShowCosts = ['С.В. - Тест', 'СОЧИ', 'КАССА'];

        foreach ($costs as $cost) {

            if (in_array($cost->name, $notShowCosts)) {
                continue;
            }

            if ($cost->name === 'СТИ') {
                continue;
            }

            $closuresList = $cost->closures->where('is_confirm', false)->sortByDesc('date');

            $closures[$cost->id]['name'] = $cost->name;
            $closures[$cost->id]['not_close'] = [];
            $closures[$cost->id]['not_split'] = [];

            foreach ($closuresList as $closure) {
                $closures[$cost->id]['not_split'][] = Carbon::parse($closure->date)->format('F Y');
            }

            $dates = $cost->items()->where('information', 'NOT LIKE', 'Закрытие месяца (%')->orderBy('date')->groupBy('date')->pluck('date')->toArray();
            foreach ($dates as $index => $date) {
                $dates[$index] = substr($date, 0,-3) . '-01';
            }
            $dates = array_unique($dates);

            $closuresDates = $cost->closures->sortBy('date')->pluck('date')->toArray();

            $diffDates = array_diff($dates, $closuresDates);
            foreach ($diffDates as $date) {
                if ($date === $nowMonth) {
                    unset($diffDates[$nowMonth]);
                    continue;
                }

                $closures[$cost->id]['not_close'][] = Carbon::parse($date)->format('F Y');
            }
        }

        $this->endProcess();

        return 0;
    }
}
