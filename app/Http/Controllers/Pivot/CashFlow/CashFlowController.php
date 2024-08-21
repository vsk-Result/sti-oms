<?php

namespace App\Http\Controllers\Pivot\CashFlow;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Models\Object\ReceivePlan;
use App\Models\TaxPlanItem;
use App\Services\ReceivePlanService;
use Illuminate\Contracts\View\View;

class CashFlowController extends Controller
{
    private ReceivePlanService $receivePlanService;

    public function __construct(ReceivePlanService $receivePlanService)
    {
        $this->receivePlanService = $receivePlanService;
    }

    public function index(): View
    {
        $planPaymentTypes = [
            'НДС',
            'Налог на прибыль',
            'НДФЛ',
            'Страховые взносы',
            'Магнитогорск комиссия за выдачу БГ',
            'Кемерово комиссия',
            'Тинькофф комиссия БГ',
            'Аэрофлот комиссия БГ',
            'Камчатка комиссия',
            'Тинькофф комиссия БГ (ГУ)',
            'Сухаревская комиссия БГ 3% (ГУ)',
            '% по кредиту',
            'Погашение ВКЛ',
            'Возврат Займа (Завидово)',
            'Возврат Займа (Камчатка)',
            'Доплата целевого аванса подрядчикам (Кемерово)',
            'Консалтинг',
            'Лизинг СТИ на ПТИ',
            'Лизинг СТИ Ресо',
            'Комиссия по кредиту за склад БАМС',
            'АХО',
            'З/П бухгалтерия',
            'Аванс (карты)',
            'З/П (карты)',
            'З/П ИТР',
            'З/П рабочие',
            'Трансфер %',
            'Стройинлок долг трансфер'
        ];

        $planPayments = TaxPlanItem::where('paid', false)->get();
        $reasons = ReceivePlan::getReasons();
        $periods = $this->receivePlanService->getPeriods();
        $objects = BObject::active()->orderBy('code')->get();
        $plans = $this->receivePlanService->getPlans(null, $periods[0]['start'], end($periods)['start']);

        return view(
            'pivots.cash-flow.index',
            compact(
                'reasons', 'periods', 'objects', 'plans', 'planPaymentTypes', 'planPayments'
            )
        );
    }
}
