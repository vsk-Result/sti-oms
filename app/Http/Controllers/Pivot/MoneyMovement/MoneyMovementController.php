<?php

namespace App\Http\Controllers\Pivot\MoneyMovement;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Object\BObject;
use Illuminate\Contracts\View\View;

class MoneyMovementController extends Controller
{
    public function index(): View
    {
        $objects = BObject::active(['27.1'])->orderBy('code')->get();
        $banks = Bank::getBanks();
        return view(
            'pivots.money-movement.index',
            compact('objects', 'banks' )
        );
    }
}
