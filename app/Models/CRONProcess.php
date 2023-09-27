<?php

namespace App\Models;

use App\Traits\HasStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CRONProcess extends Model
{
    use HasStatus;

    protected $table = 'cron_processes';

    protected $fillable = ['command', 'title', 'description', 'period', 'last_executed_date', 'last_error', 'status_id'];

    private function getStatusesList(): array
    {
        return [
            Status::STATUS_ACTIVE => 'Активен',
            Status::STATUS_BLOCKED => 'Ошибка',
            Status::STATUS_DELETED => 'Удален'
        ];
    }

    public function getLastExecutedDate(): string
    {
        if (empty($this->last_executed_date)) {
            return '';
        }

        return Carbon::parse($this->last_executed_date)->diffForHumans();
    }
}
