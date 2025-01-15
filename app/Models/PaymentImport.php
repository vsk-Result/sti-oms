<?php

namespace App\Models;

use App\Traits\HasStatus;
use App\Traits\HasUser;
use App\Traits\HasBank;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentImport extends Model
{
    use SoftDeletes, HasUser, HasStatus, HasBank;

    protected $table = 'payment_imports';

    protected $fillable = [
        'type_id', 'bank_id', 'company_id', 'created_by_user_id', 'updated_by_user_id', 'date', 'payments_count',
        'amount_pay', 'amount_receive', 'incoming_balance', 'outgoing_balance', 'file', 'description', 'status_id',
        'currency', 'currency_rate'
    ];

    const TYPE_STATEMENT = 0;
    const TYPE_CRM_COST_CLOSURE = 1;
    const TYPE_HISTORY = 2;
    const TYPE_PAYMENTS = 3;

    public static function getStatusesLists(): array
    {
        return [
            Status::STATUS_ACTIVE => 'Заполнен',
            Status::STATUS_BLOCKED => 'Не заполнен',
            Status::STATUS_DELETED => 'Удален'
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

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'import_id');
    }

    public function getDateFormatted(string $format = 'd/m/Y'): string
    {
        return Carbon::parse($this->date)->format($format);
    }

    public function getAmountPay(): string
    {
        return CurrencyExchangeRate::format($this->amount_pay, 'RUB');
    }

    public function getAmountReceive(): string
    {
        return CurrencyExchangeRate::format($this->amount_receive, 'RUB');
    }

    public function reCalculateAmountsAndCounts(): void
    {
        $payments = $this->payments;
        $this->update([
            'payments_count' => $payments->count(),
            'amount_pay' => $this->currency === 'RUB' ? $payments->where('amount', '<', 0)->sum('amount') : $payments->where('currency_amount', '<', 0)->sum('currency_amount'),
            'amount_receive' => $this->currency === 'RUB' ? $payments->where('amount', '>=', 0)->sum('amount') : $payments->where('currency_amount', '>=', 0)->sum('currency_amount'),
            'status_id' => $payments->where('status_id', Status::STATUS_BLOCKED)->count() > 0
                ? Status::STATUS_BLOCKED
                : Status::STATUS_ACTIVE,
        ]);
    }

    public function getIncomingBalance(): string
    {
        return CurrencyExchangeRate::format($this->incoming_balance ?? 0, 'RUB');
    }

    public function getOutgoingBalance(): string
    {
        return CurrencyExchangeRate::format($this->outgoing_balance ?? 0, 'RUB');
    }

    public static function getTypes(): array
    {
        return [
            self::TYPE_STATEMENT => 'Выписка',
            self::TYPE_CRM_COST_CLOSURE => 'Касса CRM',
            self::TYPE_HISTORY => 'История оплат',
            self::TYPE_PAYMENTS => 'Оплаты',
        ];
    }

    public function getType(): string
    {
        return self::getTypes()[$this->type_id];
    }

    public function isStatement()
    {
        return $this->type_id === self::TYPE_STATEMENT;
    }

    public function getFileLink(): string
    {
        return 'storage/' . $this->file;
    }

    public static function getCrmCostAuthors(): array
    {
        $authors = [];

        foreach (self::where('type_id', self::TYPE_CRM_COST_CLOSURE)->get() as $item) {
            if (empty($item->description)) {
                continue;
            }

            $author = $item->description;

            $author = str_replace('Закрытый период кассы', '', $author);
            $author = mb_substr($author, 0, mb_strpos($author, 'за '));

            if (!empty($author)) {
                $authors[$author] = $author;
            }
        }

        $authors = array_unique($authors);

        ksort($authors);

        return $authors;
    }

    public function hasInvalidBalance(): bool
    {
        if (! $this->isStatement() || $this->company->short_name === 'ПТИ') {
            return false;
        }

        return is_valid_amount_in_range($this->getBalanceOffset());
    }

    public function getBalanceOffset()
    {
        return $this->outgoing_balance - ($this->incoming_balance + $this->amount_pay + $this->amount_receive);
    }
}
