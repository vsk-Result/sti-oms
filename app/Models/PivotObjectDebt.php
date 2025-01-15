<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PivotObjectDebt extends Model
{
    use SoftDeletes;

    protected $table = 'pivot_object_debts';

    protected $fillable = [
        'date', 'object_id', 'debt_type_id', 'debt_source_id', 'filepath', 'avans', 'balance_contract', 'amount_without_nds',
        'guarantee', 'guarantee_deadline', 'amount', 'details', 'unwork_avans', 'amount_fix', 'amount_float',
    ];

    const DEBT_TYPE_CONTRACTOR = 0;
    const DEBT_TYPE_PROVIDER = 1;
    const DEBT_TYPE_SERVICE = 2;

    const DEBT_SOURCE_CONTRACTOR_1C = 0;
    const DEBT_SOURCE_CONTRACTOR_MANAGER = 1;
    const DEBT_SOURCE_PROVIDER_1C = 2;
    const DEBT_SOURCE_SERVICE_1C = 3;

    public static function getSourcesByType(int $type): array
    {
        return [
            self::DEBT_TYPE_CONTRACTOR => [
                self::DEBT_SOURCE_CONTRACTOR_1C,
                self::DEBT_SOURCE_CONTRACTOR_MANAGER,
            ],
            self::DEBT_TYPE_PROVIDER => [
                self::DEBT_SOURCE_PROVIDER_1C,
            ],
            self::DEBT_TYPE_SERVICE => [
                self::DEBT_SOURCE_SERVICE_1C,
            ]
        ][$type];
    }

    public static function getSourceName(int $source): string
    {
        return [
            self::DEBT_SOURCE_CONTRACTOR_1C => 'Из Excel таблицы 1С',
            self::DEBT_SOURCE_CONTRACTOR_MANAGER => 'Из таблицы финансового менеджера',
            self::DEBT_SOURCE_PROVIDER_1C => 'Из Excel таблицы 1С',
            self::DEBT_SOURCE_SERVICE_1C => 'Из Excel таблицы 1С',
        ][$source];
    }
}
