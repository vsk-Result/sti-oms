<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Models\Object\GeneralCost;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GeneralCostsController extends Controller
{
    public function index(Request $request): View
    {
        $filterNDS = $request->get('filter_nds', 'nds');
        $years = ['2026', '2025', '2024', '2023', '2022', '2021', '2020', '2019', '2018', '2017'];
        $codes = GeneralCost::getObjectCodesForGeneralCosts();
        $objects = BObject::whereIn('code', $codes)->orderByDesc('code')->with(['customers', 'payments' => function($q) {
            $q->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)->where('amount', '>=', 0);
        }])->get();

        $view = $filterNDS === 'nds' ? 'general-costs.index' : 'general-costs.index_without_nds';

        return view($view, compact('objects', 'years'));
    }
}
