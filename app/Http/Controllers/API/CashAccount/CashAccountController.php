<?php

namespace App\Http\Controllers\API\CashAccount;

use App\Http\Controllers\Controller;
use App\Models\CashAccount\CashAccount;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CashAccountController extends Controller
{
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
            $data['user_accounts'][] = [
                'id' => $cashAccount->id,
                'name' => $cashAccount->name,
                'responsible_id' => $cashAccount->responsible_user_id,
                'responsible_name' => $cashAccount->responsible?->name,
                'balance' => $cashAccount->getBalance(),
                'last_closed_period' => $cashAccount->closePeriods()->orderBy('period', 'desc')->first()?->period ?? null,
            ];
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
            $data['shared_accounts'][] = [
                'id' => $cashAccount->id,
                'name' => $cashAccount->name,
                'responsible_id' => $cashAccount->responsible_user_id,
                'responsible_name' => $cashAccount->responsible?->name,
                'balance' => $cashAccount->getBalance(),
                'last_closed_period' => $cashAccount->closePeriods()->orderBy('period', 'desc')->first()?->period ?? null,
            ];
        }

        return response()->json(compact('data'));
    }
}
