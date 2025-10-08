<?php

namespace App\Http\Controllers\API\CashAccount;

use App\Http\Controllers\Controller;
use App\Models\CashAccount\CashAccount;
use App\Models\User;
use App\Services\CashAccount\ClosePeriodService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CashAccountController extends Controller
{
    public function __construct(
        private ClosePeriodService $closePeriodService
    ) {}

    public function index(Request $request): JsonResponse
    {
        if (! $request->has('verify_hash')) {
            return response()->json(['error' => 'Запрос не прошел валидацию'], 403);
        }

        if ($request->get('verify_hash') !== config('qr.verify_hash')) {
            return response()->json(['error' => 'Запрос не прошел валидацию'], 403);
        }

        if (! $request->has('user_id')) {
            return response()->json(['error' => 'Отсутствует параметр user_id'], 404);
        }

        $cashAccounts = CashAccount::active()->where('responsible_user_id', $request->get('user_id'))->get();

        $data = [
            'user_accounts' => [],
            'shared_accounts' => [],
        ];

        foreach ($cashAccounts as $cashAccount) {
            $account = [
                'id' => $cashAccount->id,
                'name' => $cashAccount->name,
                'responsible_id' => $cashAccount->responsible_user_id,
                'responsible_name' => $cashAccount->responsible?->name,
                'balance' => $cashAccount->getBalance(),
                'last_closed_period' => $cashAccount->closePeriods()->orderBy('period', 'desc')->first()?->period ?? null,
                'close_periods' => [],
            ];

            $closePeriods = $this->closePeriodService->getClosePeriods($cashAccount);

            foreach ($closePeriods as $closePeriod) {
                $account['close_periods'][] = [
                    'id' => $closePeriod->id,
                    'period' => $closePeriod->period,
                    'payments_count' => $closePeriod->payments_count,
                    'payments_amount' => $closePeriod->payments_amount,
                ];
            }

            $data['user_accounts'][] = $account;
        }

        $user = User::find($request->get('user_id'));

        if (! $user) {
            return response()->json(compact('data'));
        }

        if ($user->hasRole('super-admin') || $user->can('index cash-accounts-all-view')) {
            $sharedAccounts = CashAccount::active()->where('responsible_user_id', '!=', $user->id)->get();
        } else {
            $sharedAccounts = $user->sharedCashAccounts()->get();
        }

        foreach ($sharedAccounts as $cashAccount) {
            $account = [
                'id' => $cashAccount->id,
                'name' => $cashAccount->name,
                'responsible_id' => $cashAccount->responsible_user_id,
                'responsible_name' => $cashAccount->responsible?->name,
                'balance' => $cashAccount->getBalance(),
                'last_closed_period' => $cashAccount->closePeriods()->orderBy('period', 'desc')->first()?->period ?? null,
                'close_periods' => [],
            ];

            $closePeriods = $this->closePeriodService->getClosePeriods($cashAccount);

            foreach ($closePeriods as $closePeriod) {
                $account['close_periods'][] = [
                    'id' => $closePeriod->id,
                    'period' => $closePeriod->period,
                    'payments_count' => $closePeriod->payments_count,
                    'payments_amount' => $closePeriod->payments_amount,
                ];
            }

            $data['shared_accounts'][] = $account;
        }

        return response()->json(compact('data'));
    }
}
