<?php

namespace App\Models\Helpdesk;

use Illuminate\Support\Collection;

class Priority
{
    const NOT_SELECTED_ID = 4;

    private static array $priorities = [
        [
            'id' => 1,
            'name' => 'Низкий',
            'color' => 'success'
        ],
        [
            'id' => 2,
            'name' => 'Средний',
            'color' => 'warning'
        ],
        [
            'id' => 3,
            'name' => 'Высокий',
            'color' => 'danger'
        ],
        [
            'id' => self::NOT_SELECTED_ID,
            'name' => 'Не указан',
            'color' => 'default'
        ],
    ];

    public static function getPriorities(): Collection
    {
        $priorities = [];
        foreach (static::$priorities as $priority) {
            $priorities[] = (object)$priority;
        }
        return collect($priorities);
    }

    public static function getPriority(int $priorityId): object
    {
        return (object) static::$priorities[$priorityId - 1];
    }
}
