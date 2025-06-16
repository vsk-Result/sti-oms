<?php

namespace App\Models\CashFlow;

use App\Models\Company;
use App\Models\Object\BObject;
use App\Services\FinanceReport\CreditService;
use App\Traits\HasStatus;
use App\Traits\HasUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class PlanPayment extends Model implements Audit
{
    use HasUser, HasStatus, Auditable;

    protected $table = 'cash_flow_plan_payments';

    protected $fillable = [
        'created_by_user_id', 'updated_by_user_id', 'object_id', 'name', 'status_id', 'group_id', 'from_tax_plan'
    ];

    public function entries(): HasMany
    {
        return $this->hasMany(PlanPaymentEntry::class, 'payment_id');
    }

    public function object(): BelongsTo
    {
        return $this->belongsTo(BObject::class, 'object_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(PlanPaymentGroup::class, 'group_id');
    }

    public static function getOther(): array
    {
        $company = Company::getSTI();
        $currentDate = Carbon::now();
        $creditService = new CreditService();
        $creditsFromService = $creditService->getCredits($currentDate, $company);

        $creditDebt = 0;

        foreach ($creditsFromService as $credit) {
            if ($credit['bank'] === 'ПАО "Росбанк"' && $credit['contract'] === 'Овердрафт') {
                $creditDebt = $credit['paid'] ?? 0;
                break;
            }
        }

        return [
            'Погашение овердрафта' => $creditDebt
        ];
    }
}
