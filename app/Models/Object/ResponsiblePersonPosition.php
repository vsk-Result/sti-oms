<?php

namespace App\Models\Object;

class ResponsiblePersonPosition
{
    private static array $positions = [
        [
            'id' => 1,
            'name' => 'Руководитель проекта',
        ],
        [
            'id' => 2,
            'name' => 'Заместитель руководителя проекта',
        ],
        [
            'id' => 3,
            'name' => 'Финансовый менеджер',
        ],
    ];

    public static function getPositions(): array
    {
        return self::$positions;
    }
}
