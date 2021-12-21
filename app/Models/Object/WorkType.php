<?php

namespace App\Models\Object;

class WorkType
{
    public static function getWorkTypes(): array
    {
        return [
            ['id' => 1, 'code' => 1, 'name' => 'Строительство'],
            ['id' => 2, 'code' => 2, 'name' => 'Инженерия'],
            ['id' => 3, 'code' => 3, 'name' => 'Изоляция'],
            ['id' => 4, 'code' => 4, 'name' => 'Электрика'],
            ['id' => 5, 'code' => 5, 'name' => 'Автоматика'],
            ['id' => 6, 'code' => 6, 'name' => 'Проектирование'],
            ['id' => 7, 'code' => 7, 'name' => 'Офис/Склад'],
            ['id' => 8, 'code' => 8, 'name' => 'Офис']
        ];
    }
}
