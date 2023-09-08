<?php

namespace App\Models;

use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use App\Traits\HasBank;
use App\Traits\HasStatus;
use App\Traits\HasUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class Deposit extends Model implements Audit, HasMedia
{
    use SoftDeletes, HasUser, HasStatus, HasBank, InteractsWithMedia, Auditable;

    protected $table = 'deposits';

    protected $fillable = [
        'object_id', 'created_by_user_id', 'updated_by_user_id', 'start_date',
        'end_date', 'amount', 'status_id', 'currency', 'currency_rate', 'description'
    ];

    private function getStatusesList(): array
    {
        return [
            Status::STATUS_ACTIVE => 'Активен',
            Status::STATUS_BLOCKED => 'В архиве',
            Status::STATUS_DELETED => 'Удален'
        ];
    }

    public function object(): BelongsTo
    {
        return $this->belongsTo(BObject::class, 'object_id');
    }

    public function getStartDateFormatted(string $format = 'd/m/Y'): string
    {
        return $this->start_date ? Carbon::parse($this->start_date)->format($format) : '';
    }

    public function getEndDateFormatted(string $format = 'd/m/Y'): string
    {
        return $this->end_date ? Carbon::parse($this->end_date)->format($format) : '';
    }
}
