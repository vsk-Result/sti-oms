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
    const DEBT_SOURCE_CONTRACTOR_SUPPLY = 4;
    const DEBT_SOURCE_CONTRACTOR_MANUAL = 6;
    const DEBT_SOURCE_CONTRACTOR_CLOSED_OBJECT = 9;
    const DEBT_SOURCE_PROVIDER_1C = 2;
    const DEBT_SOURCE_PROVIDER_SUPPLY = 5;
    const DEBT_SOURCE_PROVIDER_MANUAL = 7;
    const DEBT_SOURCE_SERVICE_1C = 3;
    const DEBT_SOURCE_SERVICE_MANUAL = 8;

    public static function getSourcesByType(int $type): array
    {
        return [
            self::DEBT_TYPE_CONTRACTOR => [
                self::DEBT_SOURCE_CONTRACTOR_1C,
                self::DEBT_SOURCE_CONTRACTOR_MANAGER,
                self::DEBT_SOURCE_CONTRACTOR_SUPPLY,
                self::DEBT_SOURCE_CONTRACTOR_MANUAL,
                self::DEBT_SOURCE_CONTRACTOR_CLOSED_OBJECT,
            ],
            self::DEBT_TYPE_PROVIDER => [
                self::DEBT_SOURCE_PROVIDER_1C,
                self::DEBT_SOURCE_PROVIDER_SUPPLY,
                self::DEBT_SOURCE_PROVIDER_MANUAL,
            ],
            self::DEBT_TYPE_SERVICE => [
                self::DEBT_SOURCE_SERVICE_1C,
                self::DEBT_SOURCE_SERVICE_MANUAL,
            ]
        ][$type];
    }

    public static function getSourceName(int $source): string
    {
        return [
            self::DEBT_SOURCE_CONTRACTOR_1C => 'Из Excel таблицы 1С',
            self::DEBT_SOURCE_CONTRACTOR_MANAGER => 'Из таблицы финансового менеджера',
            self::DEBT_SOURCE_CONTRACTOR_SUPPLY => 'Из Excel таблицы Богаевой',
            self::DEBT_SOURCE_CONTRACTOR_MANUAL => 'Из Excel таблицы ручного обновления',
            self::DEBT_SOURCE_CONTRACTOR_CLOSED_OBJECT => 'Из Excel таблицы закрытых объектов',
            self::DEBT_SOURCE_PROVIDER_1C => 'Из Excel таблицы 1С',
            self::DEBT_SOURCE_PROVIDER_SUPPLY => 'Из Excel таблицы Богаевой',
            self::DEBT_SOURCE_PROVIDER_MANUAL => 'Из Excel таблицы ручного обновления',
            self::DEBT_SOURCE_SERVICE_1C => 'Из Excel таблицы 1С',
            self::DEBT_SOURCE_SERVICE_MANUAL => 'Из Excel таблицы ручного обновления',
        ][$source];
    }

    public static function getManualSources(): array
    {
        return [
            self::DEBT_SOURCE_CONTRACTOR_MANUAL,
            self::DEBT_SOURCE_PROVIDER_MANUAL,
            self::DEBT_SOURCE_SERVICE_MANUAL
        ];
    }
}
