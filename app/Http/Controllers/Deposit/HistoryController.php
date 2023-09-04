<?php

namespace App\Http\Controllers\Deposit;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankGuarantee;
use App\Models\Company;
use App\Models\Contract\Contract;
use App\Models\Deposit;
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
            'company_id' => 'Компания',
            'object_id' => 'Объект',
            'organization_id' => 'Контрагент',
            'amount' => 'Сумма',
            'start_date' => 'Дата начала',
            'end_date' => 'Дата окончания',
            'status_id' => 'Статус',
            'contract_id' => 'Договор',
            'currency' => 'Валюта',
        ];
        $events = ['created' => 'Cоздание', 'updated' => 'Изменение', 'deleted' => 'Удаление'];
        $objects = BObject::pluck('name', 'id');

        if (auth()->user()->hasRole(['object-leader', 'finance-object-user'])) {
            $objects = BObject::whereIn('id', auth()->user()->objects->pluck('id'))->pluck('name', 'id');
        }

        $contracts = Contract::pluck('name', 'id');
        $statuses = [
            Status::STATUS_ACTIVE => 'Активен',
            Status::STATUS_BLOCKED => 'В архиве',
            Status::STATUS_DELETED => 'Удален'
        ];
        $banks = Bank::getBanks();
        $companies = Company::pluck('name', 'id');
        $organizations = Organization::pluck('name', 'id');
        $users = User::orderBy('name')->get();
        $auditables = [
            Deposit::class => 'Депозиты'
        ];
        $auditable = Deposit::class;

        $query = Audit::query();
        $requestData = $request->toArray();

        if (! empty($requestData['period'])) {
            $period = str_replace('/', '.', $requestData['period']);
            $startDate = substr($period, 0, strpos($period, ' '));
            $endDate = substr($period, strpos($period, ' ') + 3);

            $query->whereBetween('start_date', [Carbon::parse($startDate), Carbon::parse($endDate)]);
        }

        if (! empty($requestData['user'])) {
            $query->whereIn('user_id', $requestData['user']);
        }

        if (! empty($requestData['deposits_id'])) {
            $query->where('auditable_id', $requestData['deposits_id']);
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

        $query->where('auditable_type', Deposit::class);

        $perPage = 15;
        if (! empty($requestData['count_per_page'])) {
            $perPage = (int) preg_replace("/[^0-9]/", '', $requestData['count_per_page']);
        }

        $audits = $query->latest()->paginate($perPage)->withQueryString();

        return view(
            'deposits.history.index',
            compact(
                'audits', 'events', 'fields', 'objects', 'statuses', 'banks',
                'companies', 'organizations', 'users', 'auditables', 'auditable', 'contracts'
            )
        );
    }
}
