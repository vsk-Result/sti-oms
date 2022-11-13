<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\CRM\Cost;
use Carbon\Carbon;
use Illuminate\View\View;

class CostStatusController extends Controller
{
    public function index(): View
    {
        $closures = [];
        $nowMonth = Carbon::now()->format('Y-m') . '-01';
        $costs = Cost::where('is_archive', false)->with('closures')->get();
        $canShowSTI = [
            'stevan.nikolic@st-ing.com',
            'oksana.dashenko@st-ing.com',
            'result007@yandex.ru',
            'inna.kamennaya@dttermo.ru'
        ];
        $notShowCosts = ['С.В. - Тест', 'СОЧИ', 'КАССА'];

        foreach ($costs as $cost) {

            if (in_array($cost->name, $notShowCosts)) {
                continue;
            }

            if ($cost->name === 'СТИ') {
                if (! in_array(auth()->user()->email, $canShowSTI)) {
                    continue;
                }
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

        return view('crm-costs.index', compact('closures'));
    }
}
