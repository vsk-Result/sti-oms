<?php

namespace App\Models;

use App\Traits\HasStatus;
use App\Traits\HasUser;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class TaxPlanItem extends Model implements Audit
{
    use SoftDeletes, HasUser, HasStatus, Auditable;

    protected $table = 'tax_plan_items';

    protected $fillable = [
        'created_by_user_id', 'updated_by_user_id', 'name', 'amount', 'due_date',
        'period', 'in_one_c', 'paid', 'payment_date', 'status_id',
    ];

    public function getDueDateFormatted(): string
    {
        return $this->due_date ? Carbon::parse($this->due_date)->format('d.m.Y') : '';
    }

    public function getDueDateStatus(): array
    {
        if (empty($this->due_date) || $this->paid) {
            return ['days' => '', 'status' => ''];
        }

        $date1 = new DateTime(Carbon::now()->format('Y-m-d'));
        $date2 = new DateTime( Carbon::parse($this->due_date)->format('Y-m-d'));

        $diff = $date1->diff($date2)->days;
        if (Carbon::now()->format('Y-m-d') > Carbon::parse($this->due_date)->format('Y-m-d')) {
            $diff = -$diff;
        }

        $days = abs($diff) . trans_choice(' день | дня | дней', abs($diff));

        if ($diff < 0) {
            return ['days' => $days, 'status' => 'very-danger'];
        }

        if ($diff <= 7) {
            return ['days' => $days, 'status' => 'danger'];
        }

        if ($diff <= 14) {
            return ['days' => $days, 'status' => 'warning'];
        }

        return ['days' => '', 'status' => ''];
    }

    public function getPaymentDateFormatted(): string
    {
        return $this->payment_date ? Carbon::parse($this->payment_date)->format('d.m.Y') : '';
    }
}
