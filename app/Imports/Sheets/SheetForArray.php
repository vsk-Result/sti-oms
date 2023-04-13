<?php

namespace App\Imports\Sheets;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class SheetForArray implements ToArray, WithCalculatedFormulas
{
    public function array(array $rows) {}
}
