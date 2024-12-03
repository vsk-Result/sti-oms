<?php

namespace App\Http\Controllers\CashCheck;

use App\Http\Controllers\Controller;
use App\Models\CashCheck\CashCheck;
use App\Services\CashCheck\CashCheckService;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ManagerResetController extends Controller
{
    public function __construct(private CashCheckService $cashCheckService) {}

    public function store(CashCheck $check): RedirectResponse
    {
        $check->managers()->delete();

        $managers = $this->cashCheckService->getManagersForCheckFromExcel($check);
        $this->cashCheckService->addCheckManagers($check, $managers);

        return redirect()->back();
    }
}
