<?php

namespace App\Models;

use App\Models\Object\BObject;
use App\Traits\HasBank;
use App\Traits\HasStatus;
use App\Traits\HasUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class Payment extends Model implements Audit
{
    use SoftDeletes, HasUser, HasStatus, HasBank, Auditable;

    protected $table = 'payments';

    protected $fillable = [
        'import_id', 'company_id', 'bank_id', 'object_id', 'object_worktype_id', 'organization_sender_id',
        'organization_receiver_id', 'created_by_user_id', 'updated_by_user_id', 'type_id', 'payment_type_id', 'category',
        'code', 'description', 'date', 'amount', 'amount_without_nds', 'is_need_split', 'status_id', 'parameters',
        'currency', 'currency_rate', 'currency_amount', 'was_split'
    ];

    protected $casts = [
        'parameters' => 'json',
    ];

    const TYPE_NONE = 0;
    const TYPE_OBJECT = 1;
    const TYPE_GENERAL = 2;
    const TYPE_TRANSFER = 3;

    const CATEGORY_OPSTE = 'Накладные/Услуги';
    const CATEGORY_RAD = 'Работы';
    const CATEGORY_MATERIAL = 'Материалы';
    const CATEGORY_SALARY = 'Зарплата';
    const CATEGORY_TAX = 'Налоги';
    const CATEGORY_CUSTOMERS = 'Заказчики';
    const CATEGORY_TRANSFER = 'Трансфер';

    const PAYMENT_TYPE_CASH = 0;
    const PAYMENT_TYPE_NON_CASH = 1;

    public function import(): BelongsTo
    {
        return $this->belongsTo(PaymentImport::class, 'import_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function object(): BelongsTo
    {
        return $this->belongsTo(BObject::class, 'object_id');
    }

    public function organizationSender(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_sender_id');
    }

    public function organizationReceiver(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_receiver_id');
    }

    public function getDateFormatted(string $format = 'd/m/Y'): string
    {
        return Carbon::parse($this->date)->format($format);
    }

    public function getAmount(): string
    {
        return CurrencyExchangeRate::format($this->amount, 'RUB');
    }

    public function getAmountWithoutNDS(): string
    {
        return CurrencyExchangeRate::format($this->amount_without_nds, 'RUB');
    }

    public static function getCategories(): array
    {
        return [
            self::CATEGORY_CUSTOMERS => self::CATEGORY_CUSTOMERS,
            self::CATEGORY_SALARY => self::CATEGORY_SALARY,
            self::CATEGORY_TAX => self::CATEGORY_TAX,
            self::CATEGORY_RAD => self::CATEGORY_RAD,
            self::CATEGORY_MATERIAL => self::CATEGORY_MATERIAL,
            self::CATEGORY_TRANSFER => self::CATEGORY_TRANSFER,
            self::CATEGORY_OPSTE => self::CATEGORY_OPSTE,
        ];
    }

    public static function getTypes(): array
    {
        return [
            self::TYPE_NONE => '-',
            self::TYPE_GENERAL => 'Общее',
            self::TYPE_TRANSFER => 'Трансфер'
        ];
    }

    private function getStatusesList(): array
    {
        return [
            Status::STATUS_ACTIVE => 'Заполнен',
            Status::STATUS_BLOCKED => 'Не заполнен',
            Status::STATUS_DELETED => 'Удален'
        ];
    }

    public static function getPaymentTypes(): array
    {
        return [
            self::PAYMENT_TYPE_CASH => 'Наличный',
            self::PAYMENT_TYPE_NON_CASH => 'Безналичный'
        ];
    }

    public function getPaymentType(): string
    {
        return self::getPaymentTypes()[$this->payment_type_id];
    }

    public function getObjectId(): string
    {
        if ($this->type_id === static::TYPE_OBJECT) {
            return $this->object_id . '::' . ($this->object->isWithoutWorktype() || $this->code == '0' ? null : $this->object_worktype_id);
        }

        return $this->type_id;
    }

    public function getObject(): string
    {
        if ($this->type_id === static::TYPE_OBJECT) {
            if (! is_null($this->object_worktype_id)) {
                    return $this->object->isWithoutWorktype() || $this->code == '0'
                    ? $this->object->code
                    : $this->object->code . '.' . $this->object_worktype_id;
            }

            return $this->object->code ?? '';
        }

        switch ($this->type_id) {
            case static::TYPE_TRANSFER:    return 'Трансфер';
            case static::TYPE_GENERAL:     return 'Общее';
            default:
                break;
        }
        return '';
    }

    public function isNeedSplit(): bool
    {
        return $this->is_need_split;
    }

    public function isTransfer(): bool
    {
        return $this->type_id === static::TYPE_TRANSFER;
    }

    public function getParameter(string $key)
    {
        return $this->parameters[$key] ?? null;
    }

    public function getOrganizationId(): int|null
    {
        return $this->amount < 0 ? $this->organization_receiver_id : $this->organization_sender_id;
    }

    public function getSplitColor(): string
    {
        if (!$this->was_split) {
            return '';
        }

        // ндфл
        if (mb_strpos($this->description, 'Налог на доходы физических лиц за') !== false) {
            return 'danger';
        }

        // страховые взносы
        if (mb_strpos($this->description, 'Страховые взносы') !== false) {
            return 'warning';
        }

        // проживание
        $splitResidenceOrganizationsNames = [
            'ИП Кадинова Елена Николаевна',
            'Общество с ограниченной ответственностью "АРТИСТ ПЛЮС"',
            'АО "ИНТЕРМЕТСЕРВИС"'
        ];
        $splitResidenceOrganizations = Organization::whereIn('name', $splitResidenceOrganizationsNames)->pluck('id')->toArray();

        if (in_array($this->organization_receiver_id, $splitResidenceOrganizations)) {
            return 'primary';
        }

        // CRM зп и авансы
        if (in_array($this->code, ['7.17', '7.26', '7.17.1', '7.17.2', '7.26.1', '7.26.2']) && $this->payment_type_id === self::PAYMENT_TYPE_NON_CASH) {
            return 'success';
        }

        // CRM касса
        return 'secondary';
    }
}
