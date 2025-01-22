<?php

namespace App\Http\Controllers\API\Pivot\MoneyMovement;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Object\BObject;
use App\Models\Organization;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InfoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (! $request->has('verify_hash')) {
            return response()->json(['error' => 'Запрос не прошел валидацию'], 403);
        }

        if ($request->get('verify_hash') !== config('qr.verify_hash')) {
            return response()->json(['error' => 'Запрос не прошел валидацию'], 403);
        }

        $objectList = BObject::active(['27.1'])->orderBy('code')->get();
        $objects = [];
        foreach ($objectList as $object) {
            $objects[$object->id] = $object->getName();
        }

        $banks = Bank::getBanks();
        $paymentTypes = Payment::getPaymentTypes();

        $organizations = [];

        if ($request->has('organization_name_query')) {
            $organizations = Organization::where('name', 'LIKE', '%' . $request->get('organization_name_query') . '%')
                ->orderBy('name')
                ->pluck('name', 'id');
        }

        return response()->json([
            'objects' => $objects,
            'banks' => $banks,
            'payment_types' => $paymentTypes,
            'organizations' => $organizations
        ]);
    }
}
