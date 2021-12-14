<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Models\Bank;
use App\Models\Company;
use App\Models\Object\BObject;
use App\Models\Object\WorkType;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\PaymentImport;
use App\Services\PaymentService;
use Carbon\Carbon;
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
        $organizations = Organization::orderBy('name')->get();
        $objects = BObject::orderBy('code')->get();
        $worktypes = WorkType::getWorkTypes();
        $categories = Payment::getCategories();
        $importTypes = PaymentImport::getTypes();
        $banks = Bank::getBanks();

        $totalInfo = [];
        $payments = $this->paymentService->filterPayments($request->toArray(), true, $totalInfo);

        return view(
            'payments.index',
            compact(
                'payments', 'companies', 'objects', 'worktypes', 'organizations',
                'categories', 'importTypes', 'banks', 'totalInfo'
            )
        );
    }

    public function store(Request $request): JsonResponse
    {
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

    public function edit(Payment $payment): View
    {
        $categories = Payment::getCategories();
        $objects = Payment::getTypes() + BObject::getObjectsList();
        $companies = Company::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();
        $banks = Bank::getBanks();

        return view('payments.edit', compact('payment', 'categories', 'objects', 'companies', 'organizations', 'banks'));
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
