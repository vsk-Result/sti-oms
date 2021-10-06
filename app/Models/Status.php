<?php

namespace App\Models;

class Status
{
    const STATUS_ACTIVE = 0;
    const STATUS_BLOCKED = 1;
    const STATUS_DELETED = 2;

    public static function getStatuses(): array
    {
        return [
            static::STATUS_ACTIVE => 'Активен',
            static::STATUS_BLOCKED => 'Недоступен',
            static::STATUS_DELETED => 'Удален'
        ];
    }
}
