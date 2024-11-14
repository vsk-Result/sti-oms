<?php

namespace App\Http\Controllers\Payment;

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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Currency;

class PaymentController extends Controller
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request): View | JsonResponse
    {
        if ($request->ajax()) {
            if (empty($request->get('search')) && $request->get('type') === 'select') {
                return response()->json();
            }

            $paymentQuery = Payment::query();

            if ($request->get('type') === 'НДФЛ') {
                $paymentQuery->where('type_id', Payment::TYPE_GENERAL)
                    ->where('company_id', 1)
                    ->where(function($q) {
                        $q->where('description', 'LIKE', '%Налог на доходы физ. лиц%');
                        $q->orWhere('description', 'LIKE', '%Налог на доходы физических лиц%');
                        $q->orWhere('description', 'LIKE', '%НДФЛ%');
                    })
                    ->where('description', 'NOT LIKE', '%аренд%')
                    ->where('description', 'LIKE', '%' . $request->get('search') . '%');
            } else if ($request->get('type') === 'Страховые взносы') {
                $paymentQuery->where('type_id', Payment::TYPE_GENERAL)
                    ->where('company_id', 1)
                    ->where('description', 'LIKE', '%Страховые взносы%')
                    ->where('description', 'LIKE', '%' . $request->get('search') . '%');
            }

            $paymentList = $paymentQuery->orderByDesc('date')->take(30)->get();

            $payments = [];
            foreach ($paymentList as $payment) {
                $payments[$payment->id] = sprintf('%s | %s | %s', $payment->getDateFormatted(), $payment->getAmount(), $payment->description);
            }

            return response()->json(compact('payments'));
        }

        $objects = BObject::orderBy('code')->get();

        if (auth()->user()->hasRole(['object-leader', 'finance-object-user'])) {
            $objects = BObject::whereIn('id', auth()->user()->objects->pluck('id'))->orderBy('code')->get();
        }

        $companies = Company::orderBy('id')->get();
        $worktypes = WorkType::getWorkTypes();
        $categories = Payment::getCategories();
        $importTypes = PaymentImport::getTypes();
        $paymentTypes = Payment::getPaymentTypes();
        $banks = Bank::getBanks();
        $codes = KostCode::getCodes();
        $currencies = Currency::getCurrencies();
        $crmCostAuthors = PaymentImport::getCrmCostAuthors();

        $totalInfo = [];
        $payments = $this->paymentService->filterPayments($request->toArray(), true, $totalInfo);

        $activeOrganizations = [];
        if (! empty($request->get('organization_id'))) {
            $activeOrganizations = Organization::whereIn('id', $request->get('organization_id'))->orderBy('name')->get();
        }

        return view(
            'payments.index',
            compact(
                'payments', 'companies', 'objects', 'worktypes', 'categories',
                'importTypes', 'banks', 'totalInfo', 'activeOrganizations', 'paymentTypes', 'codes', 'currencies',
                'crmCostAuthors'
            )
        );
    }

    public function create(Request $request): View|JsonResponse
    {
        $categories = Payment::getCategories();
        $objects = Payment::getTypes() + BObject::getObjectsList();
        $companies = Company::orderBy('id')->get();
        $organizations = Organization::orderBy('name')->get();
        $banks = Bank::getBanks();
        $paymentTypes = Payment::getPaymentTypes();
        $currencies = Currency::getCurrencies();
        $codes = KostCode::getCodes();

        if ($request->ajax()) {

            $copyPayment = null;
            if ($request->has('copy_payment_id')) {
                $copyPayment = Payment::find($request->get('copy_payment_id'));
            }

            return response()->json([
                'status' => 'success',
                'payment_form' => view('payments.parts._payment_form', compact('codes', 'copyPayment', 'categories', 'objects', 'companies', 'organizations', 'banks', 'paymentTypes', 'currencies'))->render()
            ]);
        }

        return view('payments.create', compact('categories', 'objects', 'companies', 'organizations', 'banks', 'paymentTypes', 'currencies'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->ajax()) {

            $payment = $this->paymentService->createPayment($request->toArray());
            $payment->import?->reCalculateAmountsAndCounts();

            $objects = Payment::getTypes() + BObject::getObjectsList();
            $categories = Payment::getCategories();

            $paymentHtml = view('payment-imports.partials._edit_payment_table_row', compact('payment', 'objects', 'categories'))->render();

            return response()->json([
                'status' => 'success',
                'message' => 'Оплата успешно создана',
                'payment_html' => $paymentHtml
            ]);
        }

        $returnUrl = $request->get('return_url');

        $info = [
            ''
        ];

        $requestData = $request->toArray() + $info;

        $this->paymentService->prepareRequestData($requestData, null);
        $payment = $this->paymentService->createPayment($requestData);
        $payment->import?->reCalculateAmountsAndCounts();

        return redirect($returnUrl ?? route('payments.index'));
    }

    public function edit(Payment $payment, Request $request): View|JsonResponse|RedirectResponse
    {
        $categories = Payment::getCategories();
        $objects = Payment::getTypes() + BObject::getObjectsList(true);
        $companies = Company::orderBy('id')->get();
        $organizations = Organization::orderBy('name')->get();
        $banks = Bank::getBanks();
        $paymentTypes = Payment::getPaymentTypes();
        $currencies = Currency::getCurrencies();
        $codes = KostCode::getCodes();

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'payment_form' => view('payments.parts._edit_payment_form', compact('codes', 'payment', 'categories', 'objects', 'companies', 'organizations', 'banks', 'paymentTypes', 'currencies'))->render()
            ]);
        }

        if (auth()->user()->hasRole(['object-leader', 'finance-object-user'])) {
            if (! in_array($payment->object_id, auth()->user()->objects->pluck('id')->toArray())) {
                abort(403);
                return redirect()->back();
            }
        }

        return view('payments.edit', compact('payment', 'categories', 'objects', 'companies', 'organizations', 'banks', 'paymentTypes', 'currencies'));
    }

    public function update(Payment $payment, Request $request): JsonResponse|RedirectResponse
    {
        $returnUrl = $request->get('return_url');
        $this->paymentService->updatePayment($payment, $request->toArray());

        if ($request->ajax() && $this->paymentService->hasError()) {
            return response()->json([
                'status' => 'error',
                'message' => $this->paymentService->getError()
            ]);
        }

        $payment->import?->reCalculateAmountsAndCounts();

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Оплата успешно изменена'
            ]);
        }

        return redirect($returnUrl ?? route('payments.index'));
    }

    public function destroy(Payment $payment, Request $request): JsonResponse|RedirectResponse
    {
        $this->paymentService->destroyPayment($payment);
        $payment->import?->reCalculateAmountsAndCounts();

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Оплата успешно удалена'
            ]);
        }

        return redirect()->back();
    }
}
