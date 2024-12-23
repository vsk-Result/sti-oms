<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CRONProcess extends Model
{
    protected $table = 'cron_processes';

    protected $fillable = [
        'command', 'title', 'description', 'period', 'last_running_date',
        'last_executed_date', 'last_error', 'status_id'
    ];

    const STATUS_RUNNING = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR = 2;
    const STATUS_NEW = 3;

    public static function getStatuses(): array
    {
        return [
            self::STATUS_SUCCESS => 'Успешно отработал',
            self::STATUS_RUNNING => 'В работе',
            self::STATUS_ERROR => 'Завершился с ошибкой',
            self::STATUS_NEW => 'Не запущен',
        ];
    }

    public static function getStatusColors(): array
    {
        return [
            self::STATUS_SUCCESS => 'success',
            self::STATUS_RUNNING => 'warning',
            self::STATUS_ERROR => 'danger',
            self::STATUS_NEW => 'primary',
        ];
    }

    public function getStatus(): string
    {
        return self::getStatuses()[$this->status_id];
    }

    public function getStatusColor(): string
    {
        return self::getStatusColors()[$this->status_id];
    }

    public function isRunning(): bool
    {
        return $this->status_id === self::STATUS_RUNNING;
    }

    public function getLastExecutedDate(): string
    {
        if (empty($this->last_executed_date)) {
            return '';
        }

        return Carbon::parse($this->last_executed_date)->diffForHumans();
    }

    public function getLastRunningDate(): string
    {
        if (empty($this->last_running_date)) {
            return '';
        }

        return Carbon::parse($this->last_running_date)->diffForHumans();
    }
}
