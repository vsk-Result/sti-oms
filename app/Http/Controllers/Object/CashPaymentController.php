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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CashPaymentController extends Controller
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(BObject $object, Request $request): View|JsonResponse
    {
        if ($request->ajax()) {

            if ($request->get('get_type') === 'years') {
                $months = Payment::select('id', 'payment_type_id', 'object_id', 'date', \DB::raw('MONTH(date) month'))
                    ->where('date', 'LIKE', $request->get('year') . '-%')
                    ->where('payment_type_id', Payment::PAYMENT_TYPE_CASH)
                    ->where('object_id', $object->id)
                    ->get()
                    ->groupBy('month')
                    ->toArray();

                $months = array_reverse(array_keys($months));
                foreach ($months as $index => $month) {
                    if ($month < 10) {
                        $months[$index] = '0' . $month;
                    } else {
                        $months[$index] = '' . $month;
                    }
                }

                return response()->json([
                    'status' => 'success',
                    'months' => $months
                ]);
            }

            $totalInfo = [];
            $requestData = array_merge(['object_id' => [$object->id], 'payment_type_id' => [Payment::PAYMENT_TYPE_CASH]], $request->toArray());
            $payments = $this->paymentService->filterPayments($requestData, true, $totalInfo);

            return response()->json([
                'status' => 'success',
                'payments_view' => view('objects.parts._cash_payments', compact('payments', 'totalInfo'))->render(),
            ]);
        }

        $companies = Company::orderBy('name')->get();
        $objects = BObject::orderBy('code')->get();
        $worktypes = WorkType::getWorkTypes();
        $categories = Payment::getCategories();
        $importTypes = PaymentImport::getTypes();
        $paymentTypes = Payment::getPaymentTypes();
        $banks = Bank::getBanks();
        $codes = KostCode::getCodes();
        $currencies = ['RUB', 'EUR'];

        $activeOrganizations = [];
        if (! empty($request->get('organization_id'))) {
            $activeOrganizations = Organization::whereIn('id', $request->get('organization_id'))->orderBy('name')->get();
        }

        $years = Payment::select('id', 'payment_type_id', 'object_id', \DB::raw('YEAR(date) year'))
            ->where('payment_type_id', Payment::PAYMENT_TYPE_CASH)
            ->where('object_id', $object->id)
            ->get()
            ->groupBy('year')
            ->toArray();

        $years = array_reverse(array_keys($years));

        $months = [
            '01' => 'Январь',
            '02' => 'Февраль',
            '03' => 'Март',
            '04' => 'Апрель',
            '05' => 'Май',
            '06' => 'Июнь',
            '07' => 'Июль',
            '08' => 'Август',
            '09' => 'Сентябрь',
            '10' => 'Октябрь',
            '11' => 'Ноябрь',
            '12' => 'Декабрь'
        ];

        $pType = Payment::PAYMENT_TYPE_CASH;

        $paymentQuery = Payment::select('object_id', 'amount');
        $objectPayments = (clone $paymentQuery)->where('object_id', $object->id)->get();

        $object->total_pay = $objectPayments->where('amount', '<', 0)->sum('amount');
        $object->total_receive = $objectPayments->sum('amount') - $object->total_pay;
        $object->total_balance = $object->total_pay + $object->total_receive;
        $object->total_with_general_balance = $object->total_pay + $object->total_receive + $object->generalCosts()->sum('amount');
        if ($object->code === '288') {
            $object->general_balance_1 = $object->generalCosts()->where('is_pinned', false)->sum('amount');
            $object->general_balance_24 = $object->generalCosts()->where('is_pinned', true)->sum('amount');
        }

        return view(
            'objects.tabs.cash',
            compact(
                'object', 'years', 'months', 'companies', 'objects',
                'worktypes', 'categories', 'importTypes', 'paymentTypes', 'banks', 'activeOrganizations', 'pType',
                'codes', 'currencies'
            )
        );
    }
}
