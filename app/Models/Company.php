<?php

namespace App\Models;

use App\Models\Object\BObject;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes, HasStatus;

    protected $table = 'companies';

    protected $fillable = ['name', 'short_name', 'inn', 'status_id'];

    public function objects(): HasMany
    {
        return $this->hasMany(BObject::class, 'company_id');
    }

    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'company_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'company_id');
    }

    public function statements(): HasMany
    {
        return $this->hasMany(Statement::class, 'company_id');
    }
}
