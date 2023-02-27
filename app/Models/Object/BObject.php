<?php

namespace App\Models\Object;

use App\Models\BankGuarantee;
use App\Models\Contract\Act;
use App\Models\Contract\Contract;
use App\Models\CRM\ItrSalary;
use App\Models\CRM\SalaryDebt;
use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\Debt\DebtManual;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\PaymentImport;
use App\Models\Status;
use App\Models\User;
use App\Traits\HasStatus;
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

    public function getContractorDebts(): array
    {
        $debtManuals = DebtManual::where('type_id', Debt::TYPE_CONTRACTOR)->where('object_id', $this->id)->get();
        $debtImport = DebtImport::where('type_id', DebtImport::TYPE_SUPPLY)->latest('date')->first();
        $debtDTImport = DebtImport::where('type_id', DebtImport::TYPE_DTTERMO)->latest('date')->first();
        $debt1CImport = DebtImport::where('type_id', DebtImport::TYPE_1C)->latest('date')->first();
        $debtObjectImport = DebtImport::where('type_id', DebtImport::TYPE_OBJECT)->latest('date')->first();

        $existInObjectImport = $this
            ->debts()
            ->where('import_id', $debtObjectImport?->id)
            ->first();

        if ($existInObjectImport) {
            $debts = $this
                ->debts()
                ->where('import_id', $debtObjectImport?->id)
                ->where('type_id', Debt::TYPE_CONTRACTOR)
                ->orderBy(Organization::select('name')->whereColumn('organizations.id', 'debts.organization_id'))
                ->with('organization')
                ->orderBy('amount')
                ->get();
        } else {
            $debts = $this
                ->debts()
                ->whereIn('import_id', [$debtImport?->id, $debtDTImport?->id, $debt1CImport?->id, $debtObjectImport?->id])
                ->where('type_id', Debt::TYPE_CONTRACTOR)
                ->orderBy(Organization::select('name')->whereColumn('organizations.id', 'debts.organization_id'))
                ->with('organization')
                ->orderBy('amount')
                ->get();
        }

        if ($this->code === '288') {
            $result = [];

            foreach ($debts as $debt) {
                if (! isset($result[$debt->organization_id]['worktype'][$debt->object_worktype_id])) {
                    $result[$debt->organization_id]['name'] = $debt->organization->name;
                    $result[$debt->organization_id]['worktype'][$debt->object_worktype_id] = 0;
                }
                $result[$debt->organization_id]['worktype'][$debt->object_worktype_id] += $debt->amount;
            }
        } else {
            $result = [];

            foreach ($debts as $debt) {
                $id = $debt->organization_id . '::' . $debt->organization->name;
                if (! isset($result[$id])) {
                    $result[$id] = 0;
                }
                $result[$id] += $debt->amount;
            }

            foreach ($debtManuals as $debtManual) {
                $issetDebt = $debts->where('organization_id', $debtManual->organization_id)->first();

                if (! $issetDebt && isset($debtManual->organization)) {
                    $id = $debtManual->organization_id . '::' . $debtManual->organization->name;
                    $result[$id] = $debtManual->amount;
                }
            }

            asort($result);

            foreach ($result as $organization => $amount) {
                $organizationId = substr($organization, 0, strpos($organization, '::'));
                $debtManual = $debtManuals->where('organization_id', $organizationId)->first();

                if ($debtManual) {
                    $result[$organization] = $debtManual->amount;
                }
            }
        }


        return $result;
    }

    public function getProviderDebts(): array
    {
        $debtManuals = DebtManual::where('type_id', Debt::TYPE_PROVIDER)->where('object_id', $this->id)->get();
        $debtImport = DebtImport::where('type_id', DebtImport::TYPE_SUPPLY)->latest('date')->first();
        $debtDTImport = DebtImport::where('type_id', DebtImport::TYPE_DTTERMO)->latest('date')->first();
        $debt1CImport = DebtImport::where('type_id', DebtImport::TYPE_1C)->latest('date')->first();

        $debts = $this
            ->debts()
            ->whereIn('import_id', [$debtImport?->id, $debtDTImport?->id, $debt1CImport?->id])
            ->where('type_id', Debt::TYPE_PROVIDER)
            ->orderBy(Organization::select('name')->whereColumn('organizations.id', 'debts.organization_id'))
            ->with('organization')
            ->orderBy('amount')
            ->get();

        if ($this->code === '288') {
            $result = [];

            foreach ($debts as $debt) {
                if (! isset($result[$debt->organization_id]['worktype'][$debt->object_worktype_id])) {
                    $result[$debt->organization_id]['name'] = $debt->organization->name;
                    $result[$debt->organization_id]['worktype'][$debt->object_worktype_id] = 0;
                }
                $result[$debt->organization_id]['worktype'][$debt->object_worktype_id] += $debt->amount;
            }
        } else {
            $result = [];

            foreach ($debts as $debt) {
                $id = $debt->organization_id . '::' . $debt->organization->name;
                if (! isset($result[$id])) {
                    $result[$id] = 0;
                }

                $result[$id] += $debt->amount;
            }

            foreach ($debtManuals as $debtManual) {
                $issetDebt = $debts->where('organization_id', $debtManual->organization_id)->first();

                if (! $issetDebt) {
                    $id = $debtManual->organization_id . '::' . $debtManual->organization->name;
                    $result[$id] = $debtManual->amount;
                }
            }

            asort($result);

            foreach ($result as $organization => $amount) {
                $organizationId = substr($organization, 0, strpos($organization, '::'));
                $debtManual = $debtManuals->where('organization_id', $organizationId)->first();

                if ($debtManual) {
                    $result[$organization] = $debtManual->amount;
                }
            }
        }

        return $result;
    }

    public function getContractorDebtsAmount(): float
    {
        return array_sum($this->getContractorDebts());
    }

    public function getProviderDebtsAmount(): float
    {
        return array_sum($this->getProviderDebts());
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
        if (isset(self::getCodesWithoutWorktype()[$this->code])) {
            return 0;
        }
        return SalaryDebt::where('object_code', 'LIKE', '%' . $this->code. '%')->sum('amount');
    }

    public function scopeActive($query)
    {
        $exceptObjectsWithCodes = array_keys(self::getCodesWithoutWorktype());
        return $query->where('status_id', Status::STATUS_ACTIVE)->whereNotIn('code', $exceptObjectsWithCodes);
    }

    public function scopeWithoutGeneral($query)
    {
        $exceptObjectsWithCodes = array_keys(self::getCodesWithoutWorktype());
        return $query->whereNotIn('code', $exceptObjectsWithCodes);
    }
}
