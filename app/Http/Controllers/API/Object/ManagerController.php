<?php

namespace App\Http\Controllers\API\Object;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Models\Object\ResponsiblePersonPosition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (! $request->has('verify_hash')) {
            return response()->json(['error' => 'Запрос не прошел валидацию'], 403);
        }

        if ($request->get('verify_hash') !== config('qr.verify_hash')) {
            return response()->json(['error' => 'Запрос не прошел валидацию'], 403);
        }

//        $info = [
//            'managers' => $this->managerObjectService->getManagers(),
//            'export_filename' => 'managers_objects.xlsx',
//            'export_path' => config('app.url') . '/storage/public/objects-debts-manuals/managers_objects.xlsx',
//        ];
//

        $info = [];

        foreach(BObject::active()->orderByDesc('code')->get() as $object) {
            $bosses = $object->responsiblePersons()->whereIn('position_id', ResponsiblePersonPosition::getMainPositions())->orderBy('fullname')->get();
            $managers = $object->responsiblePersons()->whereIn('position_id', ResponsiblePersonPosition::getFinanceManagerPositions())->orderBy('fullname')->get();

            $item = [
                'object' => [
                    'code' => $object->code,
                    'name' => $object->name,
                ],
                'bosses' => [],
                'managers' => []
            ];

            foreach ($bosses as $boss) {
                $item['bosses'][] = [
                    'name' => $boss->fullname,
                    'email' => $boss->email,
                    'phone' => $boss->phone,
                ];
            }

            foreach ($managers as $manager) {
                $item['managers'][] = [
                    'name' => $manager->fullname,
                    'email' => $manager->email,
                    'phone' => $manager->phone,
                ];
            }

            $info[] = $item;
        }

        return response()->json(compact('info'));
    }
}
