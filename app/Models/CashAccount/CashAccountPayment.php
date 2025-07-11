<?php

namespace App\Models\CashAccount;

use App\Models\Company;
use App\Models\Object\BObject;
use App\Models\Organization;
use App\Models\Payment;
use App\Traits\HasUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CashAccountPayment extends Model implements Audit, HasMedia
{
    use Auditable, InteractsWithMedia, HasUser;

    protected $table = 'cash_account_payments';

    protected $fillable = [
        'cash_account_id', 'created_by_user_id', 'updated_by_user_id', 'company_id', 'object_id', 'type_id',
        'category', 'code', 'description', 'date', 'amount', 'status_id', 'organization_id'
    ];

    const TYPE_OBJECT = 0;
    const TYPE_TRANSFER = 1;

    const STATUS_ACTIVE = 0;
    const STATUS_NEED_CORRECT = 1;
    const STATUS_CLOSED = 2;
    const STATUS_DELETED = 3;

    public function cashAccount(): BelongsTo
    {
        return $this->belongsTo(CashAccount::class, 'cash_account_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function object(): BelongsTo
    {
        return $this->belongsTo(BObject::class, 'object_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function getDateFormatted(string $format = 'd/m/Y'): string
    {
        return Carbon::parse($this->date)->format($format);
    }

    public static function getCategories(): array
    {
        return Payment::getCategories();
    }

    public function getObjectCode(): string
    {
        if ($this->type_id === static::TYPE_OBJECT) {
            return $this->object->code ?? '';
        }

        return 'Трансфер';
    }

    public function getObjectName(): string
    {
        if ($this->type_id === static::TYPE_OBJECT) {
            return $this->object->name ?? '';
        }

        return 'Трансфер';
    }

    public function getType()
    {
        if ($this->type_id === static::TYPE_OBJECT) {
            return $this->amount < 0 ? 'Расход' : 'Приход';
        }
    }
}
