<?php

namespace App\Models\Object;

use App\Models\BankGuarantee;
use App\Models\Contract\Act;
use App\Models\Contract\Contract;
use App\Models\CRM\Avans;
use App\Models\CRM\ItrSalary;
use App\Models\CRM\SalaryDebt;
use App\Models\CRM\SalaryPaidMonth;
use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\Debt\DebtManual;
use App\Models\Guarantee;
use App\Models\GuaranteePayment;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\PaymentImport;
use App\Models\Status;
use App\Models\User;
use App\Models\Writeoff;
use App\Traits\HasStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class  BObject extends Model implements Audit
{
    use SoftDeletes, HasStatus, Auditable;

    protected $table = 'objects';

    protected $fillable = [
        'code',
        'name',
        'address',
        'photo',
        'status_id',
        'responsible_name',
        'responsible_email',
        'responsible_phone',
        'is_without_worktype',
        'closing_date'
    ];

    private function getStatusesList(): array
    {
        return [
            Status::STATUS_ACTIVE => 'Активен',
            Status::STATUS_BLOCKED => 'Закрыт',
            Status::STATUS_DELETED => 'Удален'
        ];
    }

    public function imports(): HasMany
    {
        return $this->hasMany(PaymentImport::class, 'object_id');
    }

    public function generalCosts(): HasMany
    {
        return $this->hasMany(GeneralCost::class, 'object_id');
    }

    public function writeoffs(): HasMany
    {
        return $this->hasMany(Writeoff::class, 'object_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'object_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'object_user', 'user_id', 'object_id');
    }

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'object_customer', 'customer_id', 'object_id');
    }

    public function bankGuarantees(): HasMany
    {
        return $this->hasMany(BankGuarantee::class, 'object_id');
    }

    public function guarantees(): HasMany
    {
        return $this->hasMany(Guarantee::class, 'object_id');
    }

    public function guaranteePayments(): HasMany
    {
        return $this->hasMany(GuaranteePayment::class, 'object_id');
    }

    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class, 'object_id');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'object_id');
    }

    public function acts(): HasMany
    {
        return $this->hasMany(Act::class, 'object_id');
    }

    public static function getObjectsList($needAllObjects = false): array
    {
        $result = [];
        $workTypes = WorkType::getWorkTypes();
        $objects = static::orderBy('code')->get();

        if (! $needAllObjects) {
            if (auth()->user()->hasRole(['object-leader', 'finance-object-user'])) {
                $objects = static::whereIn('id', auth()->user()->objects->pluck('id'))->orderBy('code')->get();
            }
        }

        foreach ($objects as $object) {
            if ($object->isWithoutWorktype()) {
                $result[$object->id . '::' . null] = $object->code;
            } else {
                foreach ($workTypes as $workType) {
                    $result[$object->id . '::' . $workType['id']] = $object->code . '.' . $workType['code'];
                }
            }
        }

        return $result;
    }

    public function getPhoto(): string
    {
        return $this->photo ? "/storage/$this->photo" : asset('images/blanks/object_photo_blank.jpg');
    }

    public function getName(): string
    {
        return $this->code . ' | '  . $this->name;
    }

    public function getEmployeesCount(): int
    {
//        if (! in_array($this->code, ['353', '1'])) {
//            return 0;
//        }
//
//        $request = \Http::post(config('qr.url'), [
//            'code' => $this->code === '1' ? '27' : $this->code,
//            'verifyHash' => config('qr.verify_hash')
//        ]);
//
//        if ($request->status() === 200) {
//            $data = json_decode($request->body(), true);
//            return $data['value'] ?? 0;
//        }

        return 0;
    }

    public function isWithoutWorktype(): bool
    {
        return $this->is_without_worktype;
    }

    public static function getCodesWithoutWorktype(): array
    {
        return [
            '27' => '27.1',
            '27.1' => '27.1',
            '27.7' => '27.1',
            '27.2' => '27.1',
            '27.3' => '27.3',
            '27.4' => '27.1',
            '27.5' => '27.1',
            '27.6' => '27.1',
            '27.8' => '27.8',
            '27.9' => '27.1',
            '28' => '28',
            '6' => '6',
            '0' => '0',
        ];
    }

    public function getComingAmountByPeriodFromCustomers(string $startDate, string $endDate): float
    {
        return $this
            ->payments
            ->whereIn('organization_sender_id', $this->customers->pluck('id')->toArray())
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');
    }

    public function getITRSalaryDebt(): float
    {
        $ITRSalaryObject = ItrSalary::where('kod', 'LIKE', '%' . $this->code. '%')->get();
        return $ITRSalaryObject->sum('paid') - $ITRSalaryObject->sum('total');
    }

    public function getWorkSalaryDebt(): float
    {
        $details = $this->getWorkSalaryDebtDetails();

        return $details['real']['amount'] + $details['predict']['amount'];
    }

    public function getWorkSalaryDebtDetails(): array
    {
        $details = [
            'real' => [
                'date' => '---',
                'amount' => 0,
            ],
            'predict' => [
                'date' => '---',
                'amount' => 0,
            ]
        ];

        if (isset(self::getCodesWithoutWorktype()[$this->code])) {
            return $details;
        }

        $lastPaidMonth = SalaryPaidMonth::where('is_paid', 1)->orderBy('month', 'desc')->first();

        if (!$lastPaidMonth) {
            return $details;
        }

        $details['real']['date'] = Carbon::parse($lastPaidMonth->month . '-01')->format('F Y');
        $details['predict']['date'] = Carbon::parse($lastPaidMonth->month . '-01')->addMonthNoOverflow()->format('F Y');

        $debtQuery = SalaryDebt::query()->where('object_code', 'LIKE', '%' . $this->code. '%')->where('month', $lastPaidMonth->month);

        $details['real']['amount'] = (clone $debtQuery)->sum('amount');
        $details['predict']['amount'] = -(clone $debtQuery)->sum('total_amount');

        $details['predict']['amount'] += abs((clone $debtQuery)->sum('card'));

        if ($details['real']['amount'] > 0) {
            $details['real']['amount'] = 0;
        }

        if ($details['predict']['amount'] > 0) {
            $details['predict']['amount'] = 0;
        }


        return $details;
    }

    public function scopeActive($query)
    {
        $exceptObjectsWithCodes = array_keys(self::getCodesWithoutWorktype());
        return $query->where(function($q) {
            $q->where('status_id', Status::STATUS_ACTIVE);
            $q->orWhere('code', '349');
        })->whereNotIn('code', $exceptObjectsWithCodes);
    }

    public function scopeWithoutGeneral($query)
    {
        $exceptObjectsWithCodes = array_keys(self::getCodesWithoutWorktype());
        return $query->whereNotIn('code', $exceptObjectsWithCodes);
    }
}
