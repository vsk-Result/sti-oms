<?php

namespace App\Models\Object;

use App\Models\BankGuarantee;
use App\Models\Contract\Act;
use App\Models\Contract\Contract;
use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\Payment;
use App\Models\PaymentImport;
use App\Models\User;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
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
        'responsible_phone'
    ];

    public function imports(): HasMany
    {
        return $this->hasMany(PaymentImport::class, 'object_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'object_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'object_user', 'user_id', 'object_id');
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

    public static function getObjectsList(): array
    {
        $result = [];
        $workTypes = WorkType::getWorkTypes();
        $objects = static::orderBy('code')->get();

        foreach ($objects as $object) {
            foreach ($workTypes as $workType) {
                $result[$object->id . '::' . $workType['id']] = $object->code . '.' . $workType['code'];
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

    public function getContractorDebts(): Collection
    {
        $debtImport = DebtImport::where('type_id', DebtImport::TYPE_SUPPLY)->latest('date')->first();
        $debtDTImport = DebtImport::where('type_id', DebtImport::TYPE_DTTERMO)->latest('date')->first();
        return $this->debts()->whereIn('import_id', [$debtImport?->id, $debtDTImport?->id])->where('type_id', Debt::TYPE_CONTRACTOR)->orderBy('amount')->get();
    }

    public function getProviderDebts(): Collection
    {
        $debtImport = DebtImport::where('type_id', DebtImport::TYPE_SUPPLY)->latest('date')->first();
        $debtDTImport = DebtImport::where('type_id', DebtImport::TYPE_DTTERMO)->latest('date')->first();
        return $this->debts()->whereIn('import_id', [$debtImport?->id, $debtDTImport?->id])->where('type_id', Debt::TYPE_PROVIDER)->orderBy('amount')->get();
    }

    public function getContractorDebtsAmount(): float
    {
        return $this->getContractorDebts()->sum('amount');
    }

    public function getProviderDebtsAmount(): float
    {
        return $this->getProviderDebts()->sum('amount');
    }

    public function getEmployeesCount(): int
    {
        if (! in_array($this->code, ['353', '1'])) {
            return 0;
        }

        $request = \Http::post(config('qr.url'), [
            'code' => $this->code === '1' ? '27' : $this->code,
            'verifyHash' => config('qr.verify_hash')
        ]);

        if ($request->status() === 200) {
            $data = json_decode($request->body(), true);
            return $data['value'] ?? 0;
        }

        return 0;
    }
}
