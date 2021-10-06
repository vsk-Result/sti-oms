<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasUser
{
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public static function bootHasUser()
    {
        static::creating(function ($model) {
            $model->created_by_user_id = auth()->id();
        });

        static::updating(function ($model) {
            $model->updated_by_user_id = auth()->id();
        });
    }
}
