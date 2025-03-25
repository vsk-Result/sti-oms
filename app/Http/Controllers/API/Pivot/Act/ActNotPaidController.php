<?php

namespace App\Http\Controllers\API\Pivot\Act;

use App\Http\Controllers\Controller;
use App\Models\Contract\Act;
use App\Models\Object\BObject;
use App\Services\Contract\ActService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActNotPaidController extends Controller
{
    private ActService $actService;

    public function __construct(ActService $actService)
    {
        $this->actService = $actService;
    }

    public function index(Request $request): JsonResponse
    {
        if (! $request->has('verify_hash')) {
            abort(403);
            return response()->json([], 403);
        }

        if ($request->get('verify_hash') !== config('qr.verify_hash')) {
            abort(403);
            return response()->json([], 403);
        }

        $acts = Act::orderBy('date')->get();
        $objects = BObject::whereIn('id', array_unique($acts->pluck('object_id')->toArray()))->orderBy('code', 'desc')->get();

        $info = [];
        foreach ($objects as $object) {
            $item = [
                'object_code' => $object->code,
                'object_name' => $object->name,
                'acts' => []
            ];

            foreach ($acts->where('object_id', $object->id) as $act) {

                $needPaid = $act->getLeftPaidAmount();

                if (is_valid_amount_in_range($needPaid)) {
                    $item['acts'][] = [
                        'contract_name' => $act->contract->name,
                        'number' => $act->number,
                        'date' => $act->getDateFormatted(),
                        'planned_payment_date' => $act->getPlannedPaymentDateFormatted(),
                        'amount_need_paid' => (float) $act->amount_need_paid,
                    ];
                }
            }

            if (count($item['acts']) > 0) {
                $info[] = $item;
            }
        }

        return response()->json(compact('info'));
    }
}
