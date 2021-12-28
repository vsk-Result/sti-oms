<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Company;
use App\Models\Object\BObject;
use App\Models\Object\WorkType;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\Status;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OwenIt\Auditing\Models\Audit;

class HistoryController extends Controller
{
    public function index(Request $request): View
    {
        $fields = [
            'bank_id' => 'Банк',
            'object_worktype_id' => 'Вид работ',
            'category' => 'Категория',
            'company_id' => 'Компания',
            'code' => 'Кост код',
            'object_id' => 'Объект',
            'description' => 'Описание',
            'organization_sender_id' => 'Организация отправитель',
            'organization_receiver_id' => 'Организация получатель',
            'amount' => 'Сумма',
            'type_id' => 'Тип',
        ];
        $events = ['created' => 'Cоздание', 'updated' => 'Изменение', 'deleted' => 'Удаление'];
        $objects = BObject::pluck('name', 'id');
        $statuses = Status::getStatuses();
        $types = Payment::getTypes() + [1 => 'Объект'];
        $workTypes = [];
        foreach (WorkType::getWorkTypes() as $workType) {
            $workTypes[$workType['id']] = $workType['name'];
        }
        $banks = Bank::getBanks();
        $companies = Company::pluck('name', 'id');
        $organizations = Organization::pluck('name', 'id');
        $users = User::orderBy('name')->get();
        $auditables = [
            Payment::class => 'Оплаты'
        ];
        $auditable = Payment::class;

        $query = Audit::query();
        $requestData = $request->toArray();


        if (! empty($requestData['period'])) {
            $period = str_replace('/', '.', $requestData['period']);
            $startDate = substr($period, 0, strpos($period, ' '));
            $endDate = substr($period, strpos($period, ' ') + 3);

            $query->whereBetween('created_at', [Carbon::parse($startDate), Carbon::parse($endDate)]);
        }

        if (! empty($requestData['user'])) {
            $query->whereIn('user_id', $requestData['user']);
        }

        if (! empty($requestData['payment_id'])) {
            $query->where('auditable_id', $requestData['payment_id']);
        }

        if (! empty($requestData['event'])) {
            $query->whereIn('event', $requestData['event']);
        }

        if (! empty($requestData['list_fields'])) {
            $listFields = $requestData['list_fields'];
        } else {
            $listFields = array_keys($fields);
        }

        $query->where(function($q) use($listFields) {

            foreach ($listFields as $index => $field) {
                if ($index == 0) {
                    $q->where('old_values', 'LIKE', '%' . $field . '%');
                } else {
                    $q->orWhere('old_values', 'LIKE', '%' . $field . '%');
                }
                $q->orWhere('new_values', 'LIKE', '%' . $field . '%');
            }
        });

        $query->where('auditable_type', Payment::class);

        $perPage = 15;
        if (! empty($requestData['count_per_page'])) {
            $perPage = (int) preg_replace("/[^0-9]/", '', $requestData['count_per_page']);
        }

        $audits = $query->latest()->paginate($perPage)->withQueryString();

        return view(
            'payments.history.index',
            compact(
                'audits', 'events', 'fields', 'objects', 'statuses', 'types',
                'workTypes', 'banks', 'companies', 'organizations', 'users', 'auditables', 'auditable'
            )
        );
    }
}
