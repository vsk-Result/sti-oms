<?php

namespace App\Models\Object;

use App\Models\BankGuarantee;
use App\Models\Contract\Act;
use App\Models\Contract\Contract;
use App\Models\CRM\ItrSalary;
use App\Models\CRM\SalaryDebt;
use App\Models\CRM\SalaryPaidMonth;
use App\Models\Debt\Debt;
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
use Illuminate\Support\Facades\Cache;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class BObject extends Model implements Audit
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
        'closing_date',
        'free_limit_amount'
    ];

    private function getStatusesList(): array
    {
        return [
            Status::STATUS_ACTIVE => 'Активен',
            Status::STATUS_BLOCKED => 'Закрыт',
            Status::STATUS_DELETED => 'Удален'
        ];
    }

    public function planPayments(): HasMany
    {
        return $this->hasMany(PlanPayment::class, 'object_id');
    }

    public function imports(): HasMany
    {
        return $this->hasMany(PaymentImport::class, 'object_id');
    }

    public function generalCosts(): HasMany
    {
        return $this->hasMany(GeneralCost::class, 'object_id');
    }

    public function transferService(): HasMany
    {
        return $this->hasMany(TransferService::class, 'object_id');
    }

    public function writeoffs(): HasMany
    {
        return $this->hasMany(Writeoff::class, 'object_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'object_id');
    }

    public function responsiblePersons(): HasMany
    {
        return $this->hasMany(ResponsiblePerson::class, 'object_id');
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
            if (auth()->user()->hasRole(['object-leader', 'finance-object-user', 'finance-object-user-mini'])) {
                $objects = static::whereIn('id', auth()->user()->objects->pluck('id'))->orderBy('code')->get();
            }
        }

        foreach ($objects as $object) {
            if ($object->isWithoutWorktype()) {
                $result[$object->id . '::' . null] = $object->getName();
            } else {
                $result[$object->id . '::' . null] = $object->getName();
                foreach ($workTypes as $workType) {
                    $result[$object->id . '::' . $workType['id']] = $object->code . '.' . $workType['code'];
                }
            }
        }

        return $result;
    }

    public static function getObjectCodes(): array
    {
        $result = [];
        $objects = static::orderByDesc('code')->get();

        if (auth()->user()->hasRole(['object-leader', 'finance-object-user', 'finance-object-user-mini'])) {
            $objects = static::whereIn('id', auth()->user()->objects->pluck('id'))->orderBy('code')->get();
        }

        foreach ($objects as $object) {
            $result[$object->id] = $object->getName();
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
        return $this->is_without_worktype || in_array($this->code, $this->getCodesWithoutWorktype());
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

    public function getWorkSalaryDebt(): float
    {
        $amount = 0;
        $details = $this->getWorkSalaryDebtDetails();

        foreach ($details as $detail) {
            $amount += $detail['amount'];
        }

        return min($amount, 0);
    }

    public function getITRSalaryDebt(): float
    {
        $debts = Cache::get('itr_salary_1c_data', []);

        if (! isset($debts[$this->code])) {
            return 0;
        }

        return $debts[$this->code]['total_amount'];
    }

    public function getWorkSalaryDebtDetails(): array
    {
        if (isset(self::getCodesWithoutWorktype()[$this->code])) {
            return [];
        }

        $lastNotPaid = SalaryPaidMonth::where('is_paid', 0)->orderBy('month', 'asc')->first();

        if (!$lastNotPaid) {
            return [];
        }

        $lastImported = SalaryPaidMonth::where('is_imported', 1)->orderBy('month', 'desc')->first();
        $predictAmount = - SalaryDebt::query()->where('object_code', 'LIKE', '%' . $this->code. '%')->where('month', $lastImported->month)->sum('total_amount');

        $details = [];
        $lastNotPaidMonth = $lastNotPaid->month;

        $now = Carbon::now()->format('Y-m');
        for ($i = 0; $i < 10; $i++) {
            if ($lastNotPaidMonth === $now && Carbon::now()->day < 15) {
                break;
            }

            if ($lastNotPaidMonth > $now) {
                break;
            }

            $salaryPaidMonth = SalaryPaidMonth::where('month', $lastNotPaidMonth)->first();

            $detail = [
                'date' => Carbon::parse($lastNotPaidMonth . '-01')->format('F Y'),
                'origin_date' => Carbon::parse($lastNotPaidMonth . '-01')->format('Y-m'),
                'is_real' => $salaryPaidMonth->is_imported ?? false
            ];

            $salaryQuery = SalaryDebt::query()->where('object_code', 'LIKE', '%' . $this->code. '%')->where('month', $lastNotPaidMonth);

            if (($salaryPaidMonth->is_imported ?? false)) {
                $detail['amount'] = (clone $salaryQuery)->sum('amount');
            } else {
                $detail['amount'] = $predictAmount + abs((clone $salaryQuery)->sum('card'));
            }

            $details[] = $detail;
            $lastNotPaidMonth = Carbon::parse($lastNotPaidMonth . '-01')->addMonthNoOverflow()->format('Y-m');
        }

        return $details;
    }

    public function getITRSalaryDebtDetails(): array
    {
        $debts = Cache::get('itr_salary_1c_data', []);

        if (! isset($debts[$this->code])) {
            return [];
        }

        return $debts[$this->code]['details'];
    }

    public function scopeActive($query, $withCodes = null)
    {
        $exceptObjectsWithCodes = array_keys(self::getCodesWithoutWorktype());
        return $query->where(function($q) use($withCodes) {
            $q->where('status_id', Status::STATUS_ACTIVE);
            if ($withCodes) {
                $q->orWhereIn('code', $withCodes);
            }
        })->whereNotIn('code', $exceptObjectsWithCodes);
    }

    public function scopeWithoutGeneral($query)
    {
        $exceptObjectsWithCodes = array_keys(self::getCodesWithoutWorktype());
        return $query->whereNotIn('code', $exceptObjectsWithCodes);
    }

    public static function getCodesForContractorImportDebts()
    {
        return array_merge(['000'], self::where('code', '>=', '346')->pluck('code')->toArray());
    }
}
