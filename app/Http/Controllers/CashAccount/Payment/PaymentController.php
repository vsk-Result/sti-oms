<?php

namespace App\Http\Controllers\CashAccount\Payment;

use App\Http\Controllers\Controller;
use App\Models\CashAccount\CashAccount;
use App\Models\CashAccount\CashAccountPayment;
use App\Models\CashAccount\ClosePeriod;
use App\Models\CRM\Apartment;
use App\Models\CRM\Employee;
use App\Models\Currency;
use App\Models\KostCode;
use App\Models\Object\WorkType;
use App\Models\Organization;
use App\Services\CashAccount\CashAccountService;
use App\Services\CashAccount\ClosePeriodService;
use App\Services\CashAccount\NotificationService;
use App\Services\CashAccount\Payment\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private CashAccountService $cashAccountService,
        private PaymentService $paymentService,
        private ClosePeriodService $closePeriodService,
        private NotificationService $notificationService,
    ) {}

    public function index(CashAccount $cashAccount, Request $request): View
    {
        $worktypes = WorkType::getWorkTypes();
        $objects = $cashAccount->getObjects(true);
        $statuses = [
            CashAccountPayment::STATUS_ACTIVE => 'Активные',
            CashAccountPayment::STATUS_VALID => 'Проверенные',
            CashAccountPayment::STATUS_VALIDATED => 'Проверенные и перенесенные на объект',
            CashAccountPayment::STATUS_CLOSED => 'Закрытые',
            CashAccountPayment::STATUS_WAITING => 'В ожидании трансфера',
        ];
        $categories = CashAccountPayment::getCategories();
        $codes = KostCode::getCodes();
        $currencies = Currency::getCurrencies();
        $transferCashAccounts = $this->cashAccountService->getAccountsWithoutResponsible($cashAccount);
        $closePeriods = $this->closePeriodService->getClosePeriods($cashAccount);
        $periodsToClose = $this->closePeriodService->getPeriodsToClose($cashAccount);

        $totalInfo = [];
        $requestData = array_merge(['cash_account_id' => [$cashAccount->id]], $request->toArray());
        $payments = $this->paymentService->filterPayments($requestData, true, $totalInfo);

        $activeOrganizations = [];
        if (! empty($request->get('organization_id'))) {
            $activeOrganizations = Organization::whereIn('id', $request->get('organization_id'))->orderBy('name')->get();
        }

        return view(
            'cash-accounts.payments.index',
            compact(
                'payments', 'worktypes', 'categories',
               'totalInfo', 'activeOrganizations', 'codes', 'currencies', 'cashAccount', 'transferCashAccounts',
                'closePeriods', 'periodsToClose', 'objects', 'statuses'
            )
        );
    }

    public function create(CashAccount $cashAccount, Request $request): JsonResponse
    {
        $categories = CashAccountPayment::getCategories();
        $objects = $cashAccount->getObjects();
        $organizations = Organization::orderBy('name')->get();
        $codes = KostCode::getCodes();
        $crmEmployees = Employee::getEmployeeList();
        $crmApartments = Apartment::orderBy('address', 'ASC')->pluck('address', 'id');
        $popularCodes = $cashAccount->getPopularPaymentCodes();
        $availableCodes = KostCode::getCodesForPayment();
        $itr = Cache::get('itr_list_1c_data', []);

        $copyPayment = null;
        if ($request->has('copy_payment_id')) {
            $copyPayment = CashAccountPayment::find($request->get('copy_payment_id'));
        }

        return response()->json([
            'status' => 'success',
            'payment_form' => view(
                'cash-accounts.payments.parts._payment_form',
                compact(
                    'cashAccount', 'codes', 'copyPayment', 'categories',
                    'objects', 'organizations', 'crmEmployees', 'popularCodes', 'availableCodes', 'itr',
                    'crmApartments'
                )
            )->render()
        ]);
    }

    public function store(CashAccount $cashAccount, Request $request): RedirectResponse
    {
        $requestData = array_merge(['cash_account_id' => $cashAccount->id], $request->toArray());
        $this->paymentService->createPayment($requestData);
        $this->cashAccountService->updateBalance($cashAccount);
        $this->notificationService->notify($cashAccount);

        return redirect()->back();
    }

    public function edit(CashAccount $cashAccount, CashAccountPayment $payment, Request $request): JsonResponse
    {
        $categories = CashAccountPayment::getCategories();
        $objects = $cashAccount->getObjects();
        $organizations = Organization::orderBy('name')->get();
        $codes = KostCode::getCodes();
        $crmEmployees = Employee::getEmployeeList();
        $crmApartments = Apartment::orderBy('address', 'ASC')->pluck('address', 'id');
        $popularCodes = $cashAccount->getPopularPaymentCodes();
        $availableCodes = KostCode::getCodesForPayment();
        $itr = Cache::get('itr_list_1c_data', []);

        return response()->json([
            'status' => 'success',
            'payment_form' => view(
                'cash-accounts.payments.parts._edit_payment_form',
                compact(
                    'cashAccount', 'codes', 'payment', 'categories',
                    'objects', 'organizations', 'crmEmployees', 'popularCodes', 'availableCodes', 'itr',
                    'crmApartments'
                )
            )->render()
        ]);
    }

    public function update(CashAccount $cashAccount, CashAccountPayment $payment, Request $request): RedirectResponse
    {
        $this->paymentService->updatePayment($payment, $request->toArray());
        $this->cashAccountService->updateBalance($cashAccount);

        return redirect()->back();
    }

    public function destroy(CashAccount $cashAccount, CashAccountPayment $payment): RedirectResponse
    {
        $this->paymentService->destroyPayment($payment);
        $this->cashAccountService->updateBalance($cashAccount);

        return redirect()->back();
    }
}
