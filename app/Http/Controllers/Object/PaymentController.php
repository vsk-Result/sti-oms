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
        $companies = Company::orderBy('name')->get();
        $objects = BObject::orderBy('code')->get();
        $worktypes = WorkType::getWorkTypes();
        $categories = Payment::getCategories();
        $importTypes = PaymentImport::getTypes();
        $paymentTypes = Payment::getPaymentTypes();
        $banks = Bank::getBanks();
        $codes = KostCode::getCodes();
        $currencies = Currency::getCurrencies();

        $totalInfo = [];
        $requestData = array_merge(['object_id' => [$object->id]], $request->toArray());
        $payments = $this->paymentService->filterPayments($requestData, true, $totalInfo);

        $activeOrganizations = [];
        if (! empty($request->get('organization_id'))) {
            $activeOrganizations = Organization::whereIn('id', $request->get('organization_id'))->orderBy('name')->get();
        }

        $paymentQuery = Payment::select('object_id', 'amount');
        $objectPayments = (clone $paymentQuery)->where('object_id', $object->id)->get();

        $object->total_pay = $objectPayments->where('amount', '<', 0)->sum('amount');
        $object->total_receive = $objectPayments->sum('amount') - $object->total_pay;
        $object->total_balance = $object->total_pay + $object->total_receive;
        if ($object->code === '288') {
            $object->general_balance_1 = $object->generalCosts()->where('is_pinned', false)->sum('amount');
            $object->general_balance_24 = $object->generalCosts()->where('is_pinned', true)->sum('amount');
        }
        $object->total_with_general_balance = $object->total_pay + $object->total_receive + $object->generalCosts()->sum('amount');

        return view(
            'objects.tabs.payments',
            compact(
                'payments', 'companies', 'objects', 'worktypes', 'activeOrganizations',
                'categories', 'importTypes', 'banks', 'totalInfo', 'object', 'paymentTypes', 'codes', 'currencies'
            )
        );
    }
}
