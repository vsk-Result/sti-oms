<?php

namespace App\Traits;

use App\Models\Bank;

trait HasBank
{
    public function getBankName(): string
    {
        return is_null($this->bank_id) ? '' : Bank::getBankName((int) $this->bank_id);
    }
}
