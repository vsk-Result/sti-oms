<?php

namespace App\Http\Controllers\TaxPlanItem;

use App\Http\Controllers\Controller;
use App\Models\Company;
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
            'company_id' => 'Компания',
            'name' => 'Наименование',
            'amount' => 'Сумма',
            'due_date' => 'Срок оплаты',
            'period' => 'Период',
            'in_one_c' => 'Платежка в 1С',
            'paid' => 'Статус',
            'payment_date' => 'Дата оплаты',
        ];
        $events = ['created' => 'Создание', 'updated' => 'Изменение', 'deleted' => 'Удаление'];

        $users = User::orderBy('name')->get();

        $auditables = [
            TaxPlanItem::class => 'План налогов к оплате'
        ];
        $auditable = TaxPlanItem::class;

        $companies = Company::pluck('short_name', 'id');
        $query = Audit::query();
        $requestData = $request->toArray();

        if (! empty($requestData['edit_date'])) {
            $period = explode(' - ', $requestData['edit_date']);
            $query->whereBetween('created_at', [Carbon::parse($period[0]), Carbon::parse($period[1])]);
        }

        if (! empty($requestData['payment_date'])) {
            $period = explode(' - ', $requestData['payment_date']);
            $query->whereBetween('payment_date', [Carbon::parse($period[0]), Carbon::parse($period[1])]);
        }

        if (! empty($requestData['name'])) {
            $query->where('name', 'LIKE', $requestData['name']);
        }

        if (! empty($requestData['period'])) {
            $query->where('period', 'LIKE', $requestData['period']);
        }

        if (! empty($requestData['in_one_c'])) {
            $query->whereIn('in_one_c', $requestData['in_one_c']);
        }

        if (! empty($requestData['paid'])) {
            $query->whereIn('paid', $requestData['paid']);
        }

        if (! empty($requestData['user_id'])) {
            $query->whereIn('user_id', $requestData['user_id']);
        }

        if (! empty($requestData['item_id'])) {
            $query->where('auditable_id', $requestData['item_id']);
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

        $query->where('auditable_type', TaxPlanItem::class);

        $perPage = 15;
        if (! empty($requestData['count_per_page'])) {
            $perPage = (int) preg_replace("/[^0-9]/", '', $requestData['count_per_page']);
        }

        $audits = $query->latest()->paginate($perPage)->withQueryString();

        return view(
            'tax-plan.history.index',
            compact(
                'audits', 'events', 'fields', 'users', 'auditables', 'auditable', 'companies'
            )
        );
    }
}
