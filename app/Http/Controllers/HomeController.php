<?php

namespace App\Http\Controllers;

use App\Models\CRM\Cost;
use App\Models\CRM\CostClosure;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $closures = [];
        $nowMonth = Carbon::now()->format('Y-m') . '-01';
        $costs = Cost::where('is_private', false)->where('is_visible', true)->where('is_archive', false)->with('closures')->get();

        foreach ($costs as $cost) {
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

        return view('home', compact('closures'));
    }
}
