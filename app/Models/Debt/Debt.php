<?php

namespace App\Models\Debt;

use App\Models\Company;
use App\Models\Object\BObject;
use App\Models\Organization;
use App\Traits\HasStatus;
use App\Traits\HasUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Debt extends Model
{
    use SoftDeletes, HasUser, HasStatus;

    protected $table = 'debts';

    protected $fillable = [
        'import_id', 'type_id', 'company_id', 'object_id', 'object_worktype_id', 'organization_id',
        'created_by_user_id', 'updated_by_user_id', 'date', 'amount', 'amount_without_nds', 'status_id',
        'category', 'code', 'invoice_number', 'order_author', 'description', 'comment', 'invoice_payment_due_date',
        'invoice_amount'
    ];

    const TYPE_CONTRACTOR = 0;
    const TYPE_PROVIDER = 1;

    public function import(): BelongsTo
    {
        return $this->belongsTo(DebtImport::class, 'import_id');
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
        return $this->date ? Carbon::parse($this->date)->format($format) : '';
    }

    public function getDueDateFormatted(string $format = 'd/m/Y'): string
    {
        return $this->invoice_payment_due_date ? Carbon::parse($this->invoice_payment_due_date)->format($format) : '';
    }

    public function getAmount(): string
    {
        return number_format($this->amount, 2, '.', ' ');
    }

    public function getAmountWithoutNDS(): string
    {
        return number_format($this->amount_without_nds, 2, '.', ' ');
    }

    public static function getTypes(): array
    {
        return [
            self::TYPE_CONTRACTOR => 'Подрядчик',
            self::TYPE_PROVIDER => 'Поставщик',
        ];
    }

    public function getType()
    {
        return self::getTypes()[$this->type_id];
    }

    public function getObjectId(): string
    {
        return $this->object_id . '::' . ($this->object->isWithoutWorktype() ? null : $this->object_worktype_id);
    }

    public function getObject(): string
    {
        if (! is_null($this->object_worktype_id)) {
            return $this->object->isWithoutWorktype()
                ? $this->object->code
                : $this->object->code . '.' . $this->object_worktype_id;
        }

        return $this->object->code;
    }
}
