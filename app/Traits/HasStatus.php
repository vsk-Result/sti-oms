<?php

namespace App\Traits;

use App\Models\Status;

trait HasStatus
{
    public function getStatus(): string
    {
        return Status::getStatuses()[$this->status_id];
    }

    public function isActive()
    {
        return $this->status_id === Status::STATUS_ACTIVE;
    }

    public function isBlocked()
    {
        return $this->status_id === Status::STATUS_BLOCKED;
    }

    public function isDeleted()
    {
        return $this->status_id === Status::STATUS_DELETED;
    }

    public function setActive()
    {
        $this->update([
            'status_id' => Status::STATUS_ACTIVE,
            'deleted_at' => null,
        ]);
    }

    public function setBlocked()
    {
        $this->update(['status_id' => Status::STATUS_BLOCKED]);
    }

    public function setUnblocked()
    {
        $this->setActive();
    }

    public static function bootHasStatus()
    {
        static::deleted(function ($model) {
            $model->status_id = Status::STATUS_DELETED;
        });
    }
}
