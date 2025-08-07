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
        'category', 'code', 'description', 'date', 'amount', 'status_id', 'organization_id', 'object_worktype_id',
        'additional_data'
    ];

    const TYPE_OBJECT = 0;
    const TYPE_TRANSFER = 1;
    const TYPE_REQUEST = 2;

    const STATUS_ACTIVE = 0;
    const STATUS_NEED_CORRECT = 1;
    const STATUS_CLOSED = 2;
    const STATUS_DELETED = 3;
    const STATUS_WAITING = 4;

    const TRANSFER_STATUS_WAITING = 0;
    const TRANSFER_STATUS_APPROVE = 1;
    const TRANSFER_STATUS_DECLINE = 2;

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
            if (! is_null($this->object_worktype_id)) {
                return $this->object->isWithoutWorktype() || $this->code == '0'
                    ? $this->object->code
                    : $this->object->code . '.' . $this->object_worktype_id;
            }

            return $this->object->code ?? '';
        }

        return 'Трансфер';
    }

    public function getObjectId(): string
    {
        return $this->object_id . '::' . ($this->object->isWithoutWorktype() || $this->code == '0' ? null : $this->object_worktype_id);
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

        if ($this->type_id === static::TYPE_REQUEST) {
            return 'Получение средств';
        }

        if ($this->type_id === static::TYPE_TRANSFER) {
            return 'Передача средств';
        }
    }

    public function getDescription()
    {
        $description = $this->description;
        $crmAvansData = $this->getCrmAvansData();
        $itrData = $this->getItrData();

        if (! is_null($crmAvansData['employee_name']) && $this->code === '7.8.2') {
            $description .= ', выплата аванса ' . $crmAvansData['employee_name'] . ' за ' . $crmAvansData['date'];
        }

        if (! is_null($crmAvansData['employee_name']) && $this->code === '7.9.2') {
            $description .= ', выплата зарплаты ' . $crmAvansData['employee_name'] . ' за ' . $crmAvansData['date'];
        }

        if (! is_null($itrData['name']) && $this->code === '7.8.1') {
            $description .= ', выплата аванса ' . $itrData['name'];
        }

        if (! is_null($itrData['name']) && $this->code === '7.9.1') {
            $description .= ', выплата зарплаты ' . $itrData['name'];
        }

        if ($this->isRequest()) {
            $requestData = $this->getAdditionalData('request_cash');
            $transferCashPayment = CashAccountPayment::find($requestData['transfer_payment_id']);

            $description .= ', получение средств от кассы "' . $transferCashPayment?->cashAccount?->name . '". Плановая дата: ' . Carbon::parse($requestData['date_planned'] ?? '')->format('d.m.Y');
        }

        if ($this->isTransfer()) {
            $transferData = $this->getAdditionalData('transfer_cash');
            $requestCashPayment = CashAccountPayment::find($transferData['request_payment_id']);

            $description .= ', передача средств кассе "' . $requestCashPayment?->cashAccount?->name . '". Плановая дата: ' . Carbon::parse($transferData['date_planned'] ?? '')->format('d.m.Y');
        }

        return $description;
    }

    public function getAdditionalData(string $key): array
    {
        $data = json_decode($this->additional_data, true) ?? [];
        return $data[$key] ?? [];
    }

    public function getCrmAvansData(): array
    {
        $data = $this->getAdditionalData('crm_avans');

        return [
            'id' => $data['id'] ?? null,
            'employee_id' => $data['employee_id'] ?? null,
            'employee_uid' => $data['employee_uid'] ?? null,
            'employee_name' => $data['employee_name'] ?? null,
            'date' => $data['date'] ?? null,
        ];
    }

    public function getItrData(): array
    {
        $data = $this->getAdditionalData('itr');
        return [
            'id' => $data['id'] ?? null,
            'name' => $data['name'] ?? null,
        ];
    }

    public function isRequest(): bool
    {
        return $this->type_id === self::TYPE_REQUEST;
    }

    public function getRequestStatus(): string
    {
        $requestData = $this->getAdditionalData('request_cash');

        return [
            self::TRANSFER_STATUS_WAITING => 'Ожидание',
            self::TRANSFER_STATUS_APPROVE => 'Подтверждено',
            self::TRANSFER_STATUS_DECLINE => 'Отменено',
        ][$requestData['status_id']];
    }

    public function getRequestStatusColor(): string
    {
        $requestData = $this->getAdditionalData('request_cash');

        return [
            self::TRANSFER_STATUS_WAITING => 'warning',
            self::TRANSFER_STATUS_APPROVE => 'success',
            self::TRANSFER_STATUS_DECLINE => 'danger',
        ][$requestData['status_id']];
    }

    public function isTransfer(): bool
    {
        return $this->type_id === self::TYPE_TRANSFER;
    }

    public function getTransferStatus(): string
    {
        $requestData = $this->getAdditionalData('transfer_cash');

        return [
            self::TRANSFER_STATUS_WAITING => 'Ожидание',
            self::TRANSFER_STATUS_APPROVE => 'Подтверждено',
            self::TRANSFER_STATUS_DECLINE => 'Отменено',
        ][$requestData['status_id']];
    }

    public function getTransferStatusColor(): string
    {
        $requestData = $this->getAdditionalData('transfer_cash');

        return [
            self::TRANSFER_STATUS_WAITING => 'warning',
            self::TRANSFER_STATUS_APPROVE => 'success',
            self::TRANSFER_STATUS_DECLINE => 'danger',
        ][$requestData['status_id']];
    }

    public function isWaiting(): bool
    {
        return $this->status_id === self::STATUS_WAITING;
    }

    public function hasActions(): bool
    {
        if ($this->isTransfer() || $this->isRequest()) {
            return $this->isWaiting();
        }

        return $this->cashAccount->isCurrentResponsible();
    }
}
