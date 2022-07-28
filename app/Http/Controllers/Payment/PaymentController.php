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

class PaymentController extends Controller
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request): View
    {
        $companies = Company::orderBy('name')->get();
        $objects = BObject::orderBy('code')->get();
        $worktypes = WorkType::getWorkTypes();
        $categories = Payment::getCategories();
        $importTypes = PaymentImport::getTypes();
        $paymentTypes = Payment::getPaymentTypes();
        $banks = Bank::getBanks();
        $codes = KostCode::getCodes();
        $currencies = ['RUB', 'EUR'];

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
                'importTypes', 'banks', 'totalInfo', 'activeOrganizations', 'paymentTypes', 'codes', 'currencies'
            )
        );
    }

    public function create(Request $request): View|JsonResponse
    {
        $categories = Payment::getCategories();
        $objects = Payment::getTypes() + BObject::getObjectsList();
        $companies = Company::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();
        $banks = Bank::getBanks();
        $paymentTypes = Payment::getPaymentTypes();
        $currencies = ['RUB', 'EUR'];

        if ($request->ajax()) {

            $copyPayment = null;
            if ($request->has('copy_payment_id')) {
                $copyPayment = Payment::find($request->get('copy_payment_id'));
            }

            return response()->json([
                'status' => 'success',
                'payment_form' => view('payments.parts._payment_form', compact('copyPayment', 'categories', 'objects', 'companies', 'organizations', 'banks', 'paymentTypes', 'currencies'))->render()
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

    public function edit(Payment $payment, Request $request): View|JsonResponse
    {
        $categories = Payment::getCategories();
        $objects = Payment::getTypes() + BObject::getObjectsList();
        $companies = Company::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();
        $banks = Bank::getBanks();
        $paymentTypes = Payment::getPaymentTypes();
        $currencies = ['RUB', 'EUR'];

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'payment_form' => view('payments.parts._edit_payment_form', compact('payment', 'categories', 'objects', 'companies', 'organizations', 'banks', 'paymentTypes', 'currencies'))->render()
            ]);
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
