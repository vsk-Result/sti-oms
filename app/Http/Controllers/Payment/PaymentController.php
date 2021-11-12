<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Models\Company;
use App\Models\Object\BObject;
use App\Models\Object\WorkType;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\PaymentImport;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
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
        $filterPeriod = '';
        $filterDescription = '';
        $filterCompanyId = 'all';
        $filterOrganizationSenderId = 'all';
        $filterOrganizationReceiverId = 'all';
        $filterObjectId = 'all';
        $filterObjectWorktypeId = 'all';
        $filterCategory = 'all';
        $filterImportTypeId = 'all';

        $paymentQuery = Payment::query();

        if ($request->has('period') && ! empty($request->input('period'))) {
            $period = str_replace('/', '.', $request->input('period'));
            $startDate = substr($period, 0, strpos($period, ' '));
            $endDate = substr($period, strpos($period, ' ') + 3);

            $paymentQuery->whereBetween('date', [Carbon::parse($startDate), Carbon::parse($endDate)]);
            $filterPeriod = $request->input('period');
        }

        if ($request->has('description') && ! empty($request->input('description'))) {
            $paymentQuery->where('description', 'LIKE', '%' . $request->input('description') . '%');
            $filterDescription = $request->input('description');
        }

        if ($request->has('company_id') && $request->input('company_id') !== 'all') {
            $paymentQuery->where('company_id', (int) $request->input('company_id'));
            $filterCompanyId = (int) $request->input('company_id');
        }

        if ($request->has('organization_sender_id') && $request->input('organization_sender_id') !== 'all') {
            $paymentQuery->where('organization_sender_id', (int) $request->input('organization_sender_id'));
            $filterOrganizationSenderId = (int) $request->input('organization_sender_id');
        }

        if ($request->has('organization_receiver_id') && $request->input('organization_receiver_id') !== 'all') {
            $paymentQuery->where('organization_receiver_id', (int) $request->input('organization_receiver_id'));
            $filterOrganizationReceiverId = (int) $request->input('organization_receiver_id');
        }

        if ($request->has('object_id') && $request->input('object_id') !== 'all') {
            $paymentQuery->where('object_id', (int) $request->input('object_id'));
            $filterObjectId = (int) $request->input('object_id');
        }

        if ($request->has('object_worktype_id') && $request->input('object_worktype_id') !== 'all') {
            $paymentQuery->where('object_worktype_id', (int) $request->input('object_worktype_id'));
            $filterObjectWorktypeId = (int) $request->input('object_worktype_id');
        }

        if ($request->has('category') && $request->input('category') !== 'all') {
            $paymentQuery->where('category', $request->input('category'));
            $filterCategory = $request->input('category');
        }

        if ($request->has('import_type_id') && $request->input('import_type_id') !== 'all') {
            $paymentImportsIds = PaymentImport::where('type_id', (int) $request->input('import_type_id'))->pluck('id');
            $paymentQuery->whereIn('import_id', $paymentImportsIds);
            $filterImportTypeId = (int) $request->input('import_type_id');
        }

        $companies = Company::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();
        $objects = BObject::orderBy('code')->get();
        $worktypes = WorkType::getWorkTypes();
        $categories = Payment::getCategories();
        $importTypes = PaymentImport::getTypes();

        $payments = $paymentQuery->with('company', 'createdBy', 'object', 'organizationReceiver', 'organizationSender')->orderByDesc('date')->orderByDesc('id')->paginate(30);

        return view('payments.index', compact(
            'payments', 'companies', 'objects', 'worktypes', 'organizations', 'categories', 'importTypes',
            'filterPeriod', 'filterDescription', 'filterCompanyId', 'filterOrganizationSenderId',
            'filterOrganizationReceiverId', 'filterObjectId', 'filterObjectWorktypeId', 'filterCategory',
            'filterImportTypeId'
        ));
    }

    public function store(Request $request): JsonResponse
    {
        $payment = $this->paymentService->createPayment($request->toArray());
        $payment->import?->reCalculateAmountsAndCounts();

        return response()->json([
            'status' => 'success',
            'message' => 'Оплата успешно создана',
            'payment' => [
                'id' => $payment->id,
                'update_url' => route('payments.update', $payment),
                'destroy_url' => route('payments.destroy', $payment),
            ]
        ]);
    }

    public function update(Payment $payment, Request $request): JsonResponse
    {
        $this->paymentService->updatePayment($payment, $request->toArray());
        $payment->import?->reCalculateAmountsAndCounts();

        return response()->json([
            'status' => 'success',
            'message' => 'Оплата успешно изменена'
        ]);
    }

    public function destroy(Payment $payment): JsonResponse
    {
        $this->paymentService->destroyPayment($payment);
        $payment->import?->reCalculateAmountsAndCounts();

        return response()->json([
            'status' => 'success',
            'message' => 'Оплата успешно удалена'
        ]);
    }
}
