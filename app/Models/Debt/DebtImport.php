<?php

namespace App\Models\Debt;

use App\Models\Company;
use App\Traits\HasStatus;
use App\Traits\HasUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DebtImport extends Model
{
    use SoftDeletes, HasUser, HasStatus;

    protected $table = 'debt_imports';

    protected $fillable = ['type_id', 'company_id', 'created_by_user_id', 'updated_by_user_id', 'date', 'file', 'status_id'];

    const TYPE_SUPPLY = 0;
    const TYPE_DTTERMO = 1;

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class, 'import_id');
    }

    public function getDateFormatted(string $format = 'd/m/Y'): string
    {
        return Carbon::parse($this->date)->format($format);
    }

    public function getAmount(): string
    {
        return number_format($this->amount, 2, '.', ' ');
    }

    public function getFileLink(): string
    {
        return 'storage/' . $this->file;
    }

    public static function getTypes(): array
    {
        return [
            self::TYPE_SUPPLY => 'Снабжение',
            self::TYPE_DTTERMO => 'ДТ Термо',
        ];
    }

    public function getType()
    {
        return self::getTypes()[$this->type_id];
    }
}
