<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleExport extends Model
{
    protected $table = 'schedule_exports';

    protected $fillable = [
        'status_id', 'data', 'data_hash', 'model', 'filepath', 'filename', 'name', 'send_to_email'
    ];

    const STATUS_READY = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_DONE = 2;
    const STATUS_CANCELED = 3;
    const STATUS_DELETED = 4;

    public function isInProgress(): bool
    {
        return $this->status_id === self::STATUS_IN_PROGRESS;
    }

    public function isReady(): bool
    {
        return $this->status_id === self::STATUS_READY;
    }

    public static function scopeReady($q)
    {
        return $q->where('status_id', self::STATUS_READY);
    }
}
