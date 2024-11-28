<?php

namespace App\Http\Controllers\CashCheck;

use App\Http\Controllers\Controller;
use App\Models\CashCheck\Manager;
use App\Services\CashCheck\CashCheckService;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ManagerUncheckController extends Controller
{
    public function __construct(private CashCheckService $cashCheckService) {}

    public function index(Manager $manager): RedirectResponse
    {
        $this->cashCheckService->managerUncheck($manager);

        return redirect()->back();
    }
}
