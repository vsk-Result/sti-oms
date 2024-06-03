<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Company;
use App\Models\KostCode;
use App\Models\Object\BObject;
use App\Models\Object\WorkType;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\PaymentImport;
use App\Services\PaymentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\Currency;

class PaymentController extends Controller
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(BObject $object, Request $request): View
    {
        $companies = Company::orderBy('id')->get();
        $objects = BObject::orderBy('code')->get();
        $worktypes = WorkType::getWorkTypes();
        $categories = Payment::getCategories();
        $importTypes = PaymentImport::getTypes();
        $paymentTypes = Payment::getPaymentTypes();
        $banks = Bank::getBanks();
        $codes = KostCode::getCodes();
        $currencies = Currency::getCurrencies();
        $crmCostAuthors = PaymentImport::getCrmCostAuthors();

        $totalInfo = [];
        $requestData = array_merge(['object_id' => [$object->id]], $request->toArray());
        $payments = $this->paymentService->filterPayments($requestData, true, $totalInfo);

        $activeOrganizations = [];
        if (! empty($request->get('organization_id'))) {
            $activeOrganizations = Organization::whereIn('id', $request->get('organization_id'))->orderBy('name')->get();
        }

        return view(
            'objects.tabs.payments',
            compact(
                'payments', 'companies', 'objects', 'worktypes', 'activeOrganizations',
                'categories', 'importTypes', 'banks', 'totalInfo', 'object', 'paymentTypes', 'codes', 'currencies',
                'crmCostAuthors'
            )
        );
    }
}
