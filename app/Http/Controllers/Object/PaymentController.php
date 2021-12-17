<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Company;
use App\Models\Object\BObject;
use App\Models\Object\WorkType;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\PaymentImport;
use App\Services\PaymentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(BObject $object, Request $request): View
    {
        $companies = Company::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();
        $objects = BObject::orderBy('code')->get();
        $worktypes = WorkType::getWorkTypes();
        $categories = Payment::getCategories();
        $importTypes = PaymentImport::getTypes();
        $banks = Bank::getBanks();

        $totalInfo = [];
        $payments = $this->paymentService->filterPayments($request->toArray(), true, $totalInfo);

        return view(
            'objects.tabs.payments',
            compact(
                'payments', 'companies', 'objects', 'worktypes', 'organizations',
                'categories', 'importTypes', 'banks', 'totalInfo', 'object'
            )
        );
    }
}
