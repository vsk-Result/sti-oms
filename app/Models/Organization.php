<?php

namespace App\Models;

use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use SoftDeletes, HasStatus;

    protected $table = 'organizations';

    protected $fillable = ['company_id', 'name', 'inn', 'kpp', 'status_id'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function paymentsSend(): HasMany
    {
        return $this->hasMany(Payment::class, 'organization_sender_id');
    }

    public function paymentsReceive(): HasMany
    {
        return $this->hasMany(Payment::class, 'organization_receiver_id');
    }
}
