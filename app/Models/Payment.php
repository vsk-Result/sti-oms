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

class Payment extends Model
{
    use SoftDeletes, HasUser, HasStatus, HasBank;

    protected $table = 'payments';

    protected $fillable = [
        'statement_id', 'company_id', 'bank_id', 'object_id', 'object_worktype_id', 'organization_sender_id',
        'organization_receiver_id', 'created_by_user_id', 'updated_by_user_id', 'type_id', 'payment_type_id', 'category',
        'code', 'description', 'date', 'amount', 'amount_without_nds', 'is_need_split', 'status_id'
    ];

    const TYPE_NONE = 0;
    const TYPE_OBJECT = 1;
    const TYPE_GENERAL = 2;
    const TYPE_TRANSFER = 3;

    const CATEGORY_OPSTE = 'OPSTE';
    const CATEGORY_RAD = 'RAD';
    const CATEGORY_MATERIAL = 'MATERIAL';

    const PAYMENT_TYPE_CASH = 0;
    const PAYMENT_TYPE_NON_CASH = 1;

    public function statement(): BelongsTo
    {
        return $this->belongsTo(Statement::class, 'statement_id');
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
        return number_format($this->amount, 2, '.', ' ');
    }

    public function getAmountWithoutNDS(): string
    {
        return number_format($this->amount_without_nds, 2, '.', ' ');
    }

    public static function getCategories(): array
    {
        return [
            self::CATEGORY_OPSTE => 'OPSTE',
            self::CATEGORY_RAD => 'RAD',
            self::CATEGORY_MATERIAL => 'MATERIAL'
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

    public static function getPaymentTypes(): array
    {
        return [
            self::PAYMENT_TYPE_CASH => 'Наличный',
            self::PAYMENT_TYPE_NON_CASH => 'Безналичный'
        ];
    }

    public function getObjectId(): string
    {
        return ($this->type_id === static::TYPE_OBJECT)
            ? "$this->object_id::$this->object_worktype_id"
            : $this->type_id;
    }

    public function getObject(): string
    {
        switch ($this->type_id) {
            case static::TYPE_OBJECT:      return $this->object->code . '.' . $this->object_worktype_id;
            case static::TYPE_TRANSFER:    return 'Трансфер';
            case static::TYPE_GENERAL:     return 'Общее';
            default:
                break;
        }
        return '';
    }

    public function isNeedSplit()
    {
        return $this->is_need_split;
    }
}
