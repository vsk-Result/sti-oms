<?php

namespace App\Http\Controllers\TaxPlanItem;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankGuarantee;
use App\Models\Company;
use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use App\Models\Object\WorkType;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\Status;
use App\Models\TaxPlanItem;
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
            'name' => 'Наименование',
            'amount' => 'Сумма',
            'due_date' => 'Срок оплаты',
            'period' => 'Период',
            'in_one_c' => 'Платежка в 1С',
            'payment_date' => 'Дата оплаты',
        ];
        $events = ['created' => 'Cоздание', 'updated' => 'Изменение', 'deleted' => 'Удаление'];

        $auditables = [
            TaxPlanItem::class => 'План налогов к оплате'
        ];
        $auditable = TaxPlanItem::class;

        $query = Audit::query();
        $requestData = $request->toArray();

        if (! empty($requestData['bg_period'])) {
            $period = str_replace('/', '.', $requestData['bg_period']);
            $startDate = substr($period, 0, strpos($period, ' '));
            $endDate = substr($period, strpos($period, ' ') + 3);

            $query->whereBetween('start_date', [Carbon::parse($startDate), Carbon::parse($endDate)]);
        }

        if (! empty($requestData['deposit_period'])) {
            $period = str_replace('/', '.', $requestData['deposit_period']);
            $startDate = substr($period, 0, strpos($period, ' '));
            $endDate = substr($period, strpos($period, ' ') + 3);

            $query->whereBetween('start_date_deposit', [Carbon::parse($startDate), Carbon::parse($endDate)]);
        }

        if (! empty($requestData['user'])) {
            $query->whereIn('user_id', $requestData['user']);
        }

        if (! empty($requestData['bank_guarantees_id'])) {
            $query->where('auditable_id', $requestData['bank_guarantees_id']);
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

        $query->where('auditable_type', BankGuarantee::class);

        $perPage = 15;
        if (! empty($requestData['count_per_page'])) {
            $perPage = (int) preg_replace("/[^0-9]/", '', $requestData['count_per_page']);
        }

        $audits = $query->latest()->paginate($perPage)->withQueryString();

        return view(
            'bank-guarantees.history.index',
            compact(
                'audits', 'events', 'fields', 'objects', 'statuses', 'banks',
                'companies', 'organizations', 'users', 'auditables', 'auditable', 'contracts'
            )
        );
    }
}
