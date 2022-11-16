<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Models\Payment;
use Illuminate\View\View;

class GeneralCostsController extends Controller
{
    public function index(): View
    {
        $codes = [
            '257', '268', '288', '292', '296', '298', '303', '304', '305', '308', '309', '317', '321', '322',
            '323', '325', '327', '330', '332', '333', '334', '335', '338', '339', '341', '342', '343',
            '344', '346', '349', '350', '352', '353', '358', '359', '360'
        ];
        $objects = BObject::whereIn('code', $codes)->orderByDesc('code')->with(['customers', 'payments' => function($q) {
            $q->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)->where('amount', '>=', 0);
        }])->get();

        return view('general-costs.index', compact('objects'));
    }
}
