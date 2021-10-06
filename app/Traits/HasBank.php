<?php

namespace App\Traits;

use App\Models\Bank;

trait HasBank
{
    public function getBankImportClass(): string
    {
        return Bank::getBankImportClass((int) $this->bank_id);
    }

    public function getBankName(): string
    {
        return Bank::getBankName((int) $this->bank_id);
    }
}
