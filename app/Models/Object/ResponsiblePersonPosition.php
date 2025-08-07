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
        [
            'id' => 4,
            'name' => 'Куратор проекта',
        ],
        [
            'id' => 5,
            'name' => 'Главный инженер проекта',
        ],
        [
            'id' => 6,
            'name' => 'Главный архитектор проекта',
        ],
        [
            'id' => 7,
            'name' => 'Главный кадровик',
        ],
        [
            'id' => 8,
            'name' => 'Главный по технике безопасности',
        ],
        [
            'id' => 9,
            'name' => 'Главный кладовщик',
        ],
    ];

    public static function getPositions(): array
    {
        return self::$positions;
    }

    public static function getMainPositions(): array
    {
        return [1, 2, 4];
//        return self::filterPositions([1, 2]);
    }

    public static function getFinanceManagerPositions(): array
    {
        return [3];
//        return self::filterPositions([3]);
    }

    private static function filterPositions(array $positionIds): array
    {
        return array_column(array_filter(self::getPositions(), function($p) use($positionIds) {
            return in_array($p, $positionIds);
        }), 'name');
    }
}
