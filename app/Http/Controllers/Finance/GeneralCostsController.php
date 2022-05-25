<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use Illuminate\View\View;

class GeneralCostsController extends Controller
{
    public function index(): View
    {
        $codes = [
            '257', '268', '288', '292', '296', '298', '303', '304', '305', '308', '309', '317', '321', '322',
            '323', '325', '327', '330', '332', '333', '334', '335', '338', '339', '340', '341', '342', '343',
            '344', '346', '349', '350', '352', '353', '357', '358', '359'
        ];
        $objects = BObject::whereIn('code', $codes)->orderBy('code')->with('customers', 'payments')->get();

        return view('general-costs.index', compact('objects'));
    }
}
