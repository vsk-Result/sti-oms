<?php

namespace App\Http\Controllers\Statement;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Models\Statement;
use App\Services\StatementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentSplitController extends Controller
{
    private StatementService $statementService;

    public function __construct(StatementService $statementService)
    {
        $this->statementService = $statementService;
    }

    public function store(Statement $statement, Payment $payment, Request $request): JsonResponse
    {
        $payments = $this->statementService->splitPayment($statement, $payment, $request->toArray());

        $viewRender = [];
        $isNewPayment = true;
        $categories = Payment::getCategories();
        $objects = Payment::getTypes() + BObject::getObjectsList();

        foreach ($payments as $payment) {
            $viewRender[] = view(
                'statements.partials._edit_payment_table_row',
                compact('statement', 'payment', 'categories', 'objects', 'isNewPayment')
            )->render();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Оплата успешно разбита',
            'view_render' => $viewRender
        ]);
    }
}
