<?php

namespace App\Models\Helpdesk;

use Illuminate\Support\Collection;

class Priority
{
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
