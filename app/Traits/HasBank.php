<?php

namespace App\Traits;

use App\Models\Bank;
use Illuminate\Support\Facades\Cache;

trait HasBank
{
    public function getBankName(): string
    {
        $paymentToDelete = Cache::get('payments_registry');

        if (isset($paymentToDelete[$this->object_id])) {
            if (in_array($this->id, $paymentToDelete[$this->object_id])) {
                return 'Распред. письмо';
            }
        }

        return is_null($this->bank_id) ? '' : Bank::getBankName((int) $this->bank_id);
    }
}
