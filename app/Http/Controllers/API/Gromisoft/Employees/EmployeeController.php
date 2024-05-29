<?php

namespace App\Http\Controllers\API\Gromisoft\Employees;

use App\Http\Controllers\Controller;
use App\Models\CRM\Employee;
use App\Models\CurrencyExchangeRate;
use App\Models\Loan;
use App\Models\Object\BObject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $status = 'error';

        if (! $request->has('gromisoft_token')) {
            return response()->json(['status' => $status, 'error' => 'Отсутствует токен'], 403);
        }

        $token = $request->get('gromisoft_token');
        $validToken = 'gromisoft_' . md5(date('dmY'));

        if ($token !== $validToken) {
            return response()->json(['status' => $status, 'error' => 'Невалидный токен'], 403);
        }

        $query = $request->get('query');

        if (! empty($query) && mb_strlen($query) < 3) {
            return response()->json(['status' => $status, 'error' => 'Длина query должны быть больше 2 символов'], 403);
        }

        $data = [];

        if (empty($query)) {
            $employees = Employee::where('company', 'ООО "Строй Техно Инженеринг"')
                ->where('is_engeneer', 0)
                ->orderBy('secondname')
                ->get();
        } else {
            $employees = Employee::where('company', 'ООО "Строй Техно Инженеринг"')
                ->where('is_engeneer', 0)
                ->where('secondname', 'LIKE', "%$query%")
                ->orderBy('secondname')
                ->get();
        }

        foreach ($employees as $employee) {
            $data[] = [
                'uid' => $employee->getUniqueID(),
                'fullname' => $employee->getFullName(),
                'position' => $employee->appointment,
                'status' => $employee->status,
            ];
        }

        $status = 'success';

        return response()->json(compact('status', 'data'));
    }
}
