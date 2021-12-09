<?php

namespace App\Imports\Sheets;

use Maatwebsite\Excel\Concerns\ToArray;

class SheetForArray implements ToArray
{
    public function array(array $rows) {}
}
